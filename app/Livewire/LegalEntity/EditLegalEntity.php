<?php

declare(strict_types=1);

namespace App\Livewire\LegalEntity;

use Log;
use Exception;
use Illuminate\Support\Arr;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Enums\License\Type as LicenseType;
use App\Models\LegalEntity as LegalEntityModel;

class EditLegalEntity extends LegalEntity
{
    public function mount(?LegalEntityModel $legalEntity = null): void
    {
        $this->legalEntity = $this->getLegalEntity();

        parent::mount();

        $this->getLegalEntityForm();
    }

    /**
     * Try to get the LegalEntity assigned for the user
     *
     * @return LegalEntityModel|null
     */
    protected function getLegalEntity(): ?LegalEntityModel
    {
        return legalEntity()?->loadMissing(['licenses', 'addresses', 'phones', 'revisions']) ?? null;
    }

    protected function setLegalEntity(): bool
    {
        $isNotNew = parent::setLegalEntity();

        if ($isNotNew) {
            $address = data_get($this->legalEntity->toArray(), 'addresses.0', []);

            $this->mergeAddress($this->convertArrayKeysToCamelCase($address));
        }

        return $isNotNew;
    }

    /**
     * Retrieves the legal entity form data.
     */
    protected function getLegalEntityForm(): void
    {
        $this->setLegalEntity(); // Retrieve basic legal entity data
        $this->getLicenseForm(); // Get the license form data
        $this->getArchiveForm(); // Get the archive form data
        $this->getOwnerLegalEntity(); // Get the owner's legal entity data
        $this->getAccreditationForm(); // Get the accreditation form data status
        $this->getBeneficiaryForm(); // Get the beneficiary form data status
        $this->getReceiverFundsCodeForm(); // Get the receiver funds code form data status
    }

    /**
     * Retrieves and sets only specific fields related to the license from the legal entity form.
     */
    protected function getLicenseForm(): void
    {
        $licenses = $this->legalEntity->licenses()->get();

        $license = $licenses->filter(function ($item) {
            return $item->type->name === LicenseType::MSP->value || $item->type->name === LicenseType::PHARMACY->value;
        })->first();

        if ($license) {
            $this->legalEntityForm->license = Arr::only(
                $this->convertArrayKeysToCamelCase($license->toArray()),
                [
                    'uuid',
                    'type',
                    'licenseNumber',
                    'issuedBy',
                    'issuedDate',
                    'expiryDate',
                    'activeFromDate',
                    'whatLicensed',
                    'orderNo'
                ]
            );
        }
    }

    /**
     * Retrieves and formats specific fields from the archive form.
     */
    protected function getArchiveForm(): void
    {
         // if the legal entity has an archive, the 'archivationShow' property is set to true
        if (!empty($this->legalEntityForm->archive)) {
            $this->legalEntityForm->archivationShow = true;
        }
    }

    /**
     * Get the accreditation status of the legal entity
     * (if the legal entity has an accreditation, the 'accreditationShow' property is set to true)
     *
     * @return void
     */
    protected function getAccreditationForm(): void
    {
        if (!empty($this->legalEntityForm->accreditation) && $this->legalEntityForm->accreditation['category'] !== null) {
            $this->legalEntityForm->accreditationShow = true;
        }
    }

    /**
     * If the legal entity has an beneficiary, the 'beneficiaryShow' property is set to true
     */
    protected function getBeneficiaryForm(): void
    {
        if (!empty($this->legalEntityForm->beneficiary)) {
            $this->legalEntityForm->beneficiaryShow = true;
        }
    }

    /**
     * If the legal entity has an beneficiary, the 'receiverFundsCodeShow' property is set to true
     */
    protected function getReceiverFundsCodeForm(): void
    {
        if (!empty($this->legalEntityForm->receiverFundsCode)) {
            $this->legalEntityForm->receiverFundsCodeShow = true;
        }
    }

    protected function getOwnerLegalEntity(): void
    {
        $owner = $this->legalEntity->getOwner();

        if (!$owner->exists()) {
            return;
        }

        $ownerData = $this->prepareOwnerData($owner);

        $this->legalEntityForm->owner = array_merge($this->legalEntityForm->owner ?? [], $ownerData);
    }

    private function prepareOwnerData(Employee $owner): array
    {
        $ownerData = $owner->party->toArray() ?? [];

        $ownerData['phones'] = $owner->party->phones->toArray() ?? [];
        $ownerData['documents'] = $this->prepareDocumentsData($owner->party->documents->toArray());
        $ownerData['position'] = $owner->position;
        $ownerData['employee_uuid'] = $owner->uuid;
        $ownerData['employee_id'] = $owner->id;
        $ownerData['email'] = $owner->user->email;
        $ownerData['party_id'] = $owner->partyId;

        // TODO: remove it when all other entity will use the same date format
        $ownerData['birthDate'] = convertToAppDateFormat($ownerData['birthDate']);

        return $ownerData;
    }

    private function prepareDocumentsData(array $documents): array
    {
        if (empty($documents)) {
            return [];
        }

        return $this->convertArrayKeysToCamelCase($documents[0]);
    }

    public function updateLegalEntity()
    {
        /*
         * This is need by Livewire behavior.
         * On the first render, mount() runs and assigns $this->legalEntity.
         * On subsequent requests (e.g., when clicking synchronize button), Livewire does NOT run mount() again
         * and does NOT rehydrate protected typed properties.
         * Code below allows to ensure that property is set before use.
         */
        $this->legalEntity ??= $this->getLegalEntity();

        if (Auth::user()->cannot('edit', $this->legalEntity)) {
            $this->dispatchErrorMessage(__('legal-entity.policy.deny.edit'));

            return null;
        }

        $this->legalEntityForm->allFieldsValidate();
        $ownerPartyId = Arr::pull($this->legalEntityForm->owner, 'party_id') ?? null;

        if ($this->getErrorBag()->isNotEmpty()) {
            $this->dispatchBrowserEvent('scroll-to-error');
        }

        // TODO: until refactoring
        if (! $result = $this->signLegalEntity()) {
            return;
        }

        $data = $result['request'];

        $data['owner']['working_experience'] = $this->legalEntityForm->owner['workingExperience'] ?? null;
        $data['owner']['about_myself'] = $this->legalEntityForm->owner['aboutMyself'] ?? null;
        $data['owner']['party_id'] = $ownerPartyId;

        $response = $this->filterUnprovidedFields($result['response'], $data);

        try {
            /**
             * The code below is need to save new client_secret if ESOZ returns successfull response
             * Without it next login may be impossible!
             */
            $legalEntity = LegalEntityModel::where(['uuid' => $response['data']['uuid'] ])->first();

            $legalEntity->clientSecret = $response['urgent']['security']['client_secret'] ?? $response['urgent']['security']['secret_key'] ?? null;

            $legalEntity->save();
            $legalEntity->refresh();

            DB::transaction(function () use ($response, $data) {
                $this->modifyLegalEntity($response);

                try {
                    $this->createEmployeeRequest($this->legalEntity, $data, $response['urgent']['employee_request_id']);
                } catch (Exception $err) {
                    throw new Exception('Error: createEmployeeRequest: ' . $err->getMessage(), $err->getCode());
                }
            });
        } catch (Exception $err) {
            Log::error(__('forms.errors.update_data', [], 'en'), ['error' => $err->getMessage()]);

            $this->dispatchErrorMessage(__('forms.errors.update_data'));

            return null;
        }

        return Redirect::route('legal-entity.edit', [legalEntity()])->with('success', __('forms.update_successfull')) ?? null;
    }

    public function render()
    {
        return view('livewire.legal-entity.edit-legal-entity', ['isEdit' => true]);
    }
}
