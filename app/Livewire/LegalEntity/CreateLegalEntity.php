<?php

namespace App\Livewire\LegalEntity;

use Exception;
use App\Models\License;
use App\Models\Relations\Phone;
use App\Models\Relations\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Repositories\PhoneRepository;
use App\Repositories\AddressRepository;
use Illuminate\Validation\ValidationException;

class CreateLegalEntity extends LegalEntity
{
    /**
     * @var int The current step of the process
     */
    protected array $steps = [
        'index' => 1,
        'accreditationShow' => false,
        'archivationShow' => false
    ];

    public string $validationErrorStep = '';

    /**
     * @return void set cache keys
     */
    public function boot(
        AddressRepository $addressRepository,
        PhoneRepository $phoneRepository
    ): void {
        parent::boot($addressRepository, $phoneRepository);

        $this->getLegalEntity();

        $this->getCurrentStepFromCache();
    }

    public function mount(): void
    {
        parent::mount();

        $this->getOwnerFields();

        $this->legalEntityForm->owner['phones'] = $this->legalEntityForm->owner['phones'] ?? [];

        $this->legalEntityForm->owner['noTaxId'] = $this->legalEntityForm->owner['noTaxId'] ?? false;

        $this->setOwnerFromCache();
    }

    /**
     * Set the owner information from the cache if available.
     */
    private function setOwnerFromCache(): void
    {
        // Check if the owner information is available in the cache and the user is not a legal entity
        if (Cache::has($this->ownerCacheKey) && !Auth::user()->legalEntity) {
            $this->legalEntityForm->owner = Cache::get($this->ownerCacheKey); // Set the owner information from cache
        }
    }

    /**
     * Set the currentStep information from the cache if available.
     */
    private function getCurrentStepFromCache(): void
    {
        // Check if the information about step state is available in the cache
        if (Cache::has($this->stepCacheKey)) {
            $this->steps = Cache::get($this->stepCacheKey); // Get the current steps information from cache

            $this->legalEntityForm->accreditationShow = $this->steps['accreditationShow'];
            $this->legalEntityForm->archivationShow = $this->steps['archivationShow'];
        }
    }

    /**
     * Set the currentStep information from the cache if available.
     */
    private function putCurrentStepToCache(): void
    {
        $this->steps['accreditationShow'] = $this->legalEntityForm->accreditationShow;
        $this->steps['archivationShow'] = $this->legalEntityForm->archivationShow;

        // Check if the information about step state is available in the cache or step data has been changed
        Cache::put($this->stepCacheKey, $this->steps, now()->days(90));
    }

    /**
     * Increases the current step of the process.
     * Resets the error bag, validates the data, increments the current step, puts the legal entity in cache,
     * and ensures the current step does not exceed the total steps.
     * This will automatically switches to the step's form on the web page.
     *
     * @throws ValidationException
     */
    public function nextStep($activeStep): bool
    {
        $this->resetErrorBag();

        $this->validateData($activeStep);

        $this->putLegalEntityInCache();

        if ($activeStep === $this->steps['index']) {
            $this->increaseStep();
        }

        return true;
    }

    /**
     * Increase step number and save it to the $this->currentStep array
     * Also appropriate step's key will saved also
     *
     * @return void
     */
    protected function increaseStep(): void
    {
        $this->steps['index']++;

        $this->putCurrentStepToCache();
    }

    /**
     * @throws ValidationException
     */
    protected function validateData($activeStep = null): void
    {
        $stepNumber = $activeStep ?? $this->steps['index'];

        match ($stepNumber) {
            1 => $this->stepEdrpou(),
            2 => $this->stepOwner(),
            3 => $this->stepContact(),
            4 => $this->stepAddress(),
            5 => $this->stepAccreditation(),
            6 => $this->stepLicense(),
            7 => $this->stepAdditionalInformation(),
            8 => $this->stepSignificancy(),
            default => null,
        };
    }

    // TODO: implement in the future release when EDRPOU will validate from outside also
    protected function saveLegalEntityFromExistingData($data): void
    {
        $normalizedData = [];

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'id':
                        $normalizedData['uuid'] = $value;
                        break;
                    case 'residence_address':
                        $normalizedData['residence_address'] = $value;
                        break;
                    case 'edr':
                        foreach ($data['edr'] as $edrKey => $edrValue) {
                            $normalizedData[$edrKey] = $edrValue;
                        }
                        break;
                    default:
                        $normalizedData[$key] = $value;
                        break;
                }
            }

            $this->legalEntity->fill($normalizedData);

            $this->legalEntityForm->fill($normalizedData);

            if (!Cache::has($this->entityCacheKey) || $this->checkChanges()) {
                Cache::put($this->entityCacheKey, $this->legalEntity, now()->days(90));
            }
        }
    }

    /**
     * Update the legal entity in the cache if changes are detected or it doesn't exist already.
     */
    private function putLegalEntityInCache(): void
    {
        // Convert all camelCase keys to snake_case because the Legal Entity model uses snake_case
        $formData = $this->convertArrayKeysToSnakeCase($this->legalEntityForm->toArray());

        $fillableData = array_intersect_key($formData, array_flip([
            'accreditation',
            'archive',
            'beneficiary',
            'edrpou',
            'email',
            'receiver_funds_code',
            'residence_address',
            'type',
            'website'
        ]));

        // Fill the legal entity model with data from the form
        $this->legalEntity->fill($fillableData);

        // Create the new Address (rely on $this->address)
        $address = new Address();
        $address->fill($this->address);

        // Associate the legal entity with the address
        $this->legalEntity->setRelation('address', $address);

        // Create the new License model bsed on the form data
        $license = new License();
        $license->fill($formData['license']);

        // Attach the newly created License model to the current LegalEntity model
        $this->legalEntity->setRelation('licenses', $license);

        // Associate the legal entity with the phones
        if (!empty($formData['phones'])) {
            $phoneCollection = collect($formData['phones'])->map(function($phone) {
                $instance = new Phone();
                $instance->fill($phone);

                return $instance;
            });

            $this->legalEntity->setRelation('phones', $phoneCollection);
        }

        // Check if the entity is not in the cache or if changes are detected
        if (!Cache::has($this->entityCacheKey) || $this->checkChanges()) {
            // Put the legal entity in the cache with a 90-day expiration
            Cache::put($this->entityCacheKey, $this->legalEntity, now()->days(90));
        }
    }

    /**
     * Check if there are changes in the Legal Entity attributes by comparing with cached data.
     *
     * @return bool Returns true if Legal Entity attributes have changed, false otherwise.
     */
    private function checkChanges(): bool
    {
        // Check if entity cache exists
        if (Cache::has($this->entityCacheKey)) {
            $cachedLegalEntity = Cache::get($this->entityCacheKey);

            $legalEntity = $this->flattenArray($this->getAllAttributes($this->legalEntity));

            $cachedLegalEntity = $this->flattenArray($this->getAllAttributes(Cache::get($this->entityCacheKey)));

            // If the Legal Entity has not changed, return false
            if (!empty(array_diff_assoc($legalEntity,$cachedLegalEntity)) ||
                !empty(array_diff_assoc($cachedLegalEntity, $legalEntity))
            ) {
                return true; // Legal Entity has changed
            }

            return false; // Legal Entity has not changed
        }

        return true; // Legal Entity has changed
    }

    /**
     * Check if the Legal Entity owner has changed.
     *
     * @return bool Returns true if the Legal Entity owner has changed, false otherwise.
     */
    private function checkOwnerChanges(): bool
    {
        // Check if the owner information is cached
        if (Cache::has($this->ownerCacheKey)) {
            $cachedOwner = Cache::get($this->ownerCacheKey);

            $legalEntityOwner = $this->legalEntityForm->owner;

            // Compare the cached owner with the current owner
            if (serialize($cachedOwner) === serialize($legalEntityOwner)) {
                return false; // No change in Legal Entity owner
            }
        }

        return true; // Return true if the Legal Entity owner has changed
    }

    /* - STEPS - */

    // Step #1 set EDRPOU number
    private function stepEdrpou(): void
    {
        $this->legalEntityForm->rulesForEdrpou();

        //TODO: Метод для перевірки ЕДРПОУ getLegalEntity
        $getLegalEntity = [];

        if (!empty($getLegalEntity)) {
            $this->saveLegalEntityFromExistingData($getLegalEntity);
        }
    }

    // Step #2 Create Owner
    private function stepOwner(): void
    {
        $this->legalEntityForm->rulesForOwner();

        // Check if the owner information is available in the cache
        $personData = $this->legalEntityForm->owner;

        // Store the owner information in the cache
        if ($this->checkOwnerChanges()) {
            Cache::put($this->ownerCacheKey, $personData, now()->days(90));
        }
    }

    // Step #3 Create/Update Contact[Phones, Email,beneficiary,receiver_funds_code]
    private function stepContact(): void
    {
        $this->legalEntityForm->rulesForContact();
    }

    // Step #4 Create/Update Address
    private function stepAddress(): void
    {
        $this->legalEntityForm->rulesForAddresses();
    }

    // Step #5 Create/Update Accreditation
    private function stepAccreditation(): void
    {
        if ($this->legalEntityForm->accreditationShow) {
            $this->legalEntityForm->rulesForAccreditation();
        }

        $this->putCurrentStepToCache(); // Only for save $this->legalEntityForm->accreditationShow state
    }

    // Step #6 Create/Update License
    private function stepLicense(): void
    {
        $this->legalEntityForm->license['type'] = 'MSP';

        $this->legalEntityForm->rulesForLicense();
    }

    // Step #7 Create/Update Additional Information
    private function stepAdditionalInformation(): void
    {
        if($this->legalEntityForm->archivationShow) {
            $this->legalEntityForm->rulesForAdditionalInformation();
        }

        $this->putCurrentStepToCache(); // Only for save $this->legalEntityForm->archivationShow state
    }

    // Step #8 KEP Significancy (called on creating new Legal Entity only)
    private function stepSignificancy(): void
    {
        $this->legalEntityForm->rulesForSignificancy();
    }

    /**
     * Summary of validationRequest
     *
     * @return bool
     */
    protected function validationRequest(): bool
    {
        $step = '';
        $field = '';

        $stepNames = [
            'edrpou' => __('forms.edrpou'),
            'owner' => __('forms.owner'),
            'contact' => __('forms.contacts'),
            'address' => __('forms.address'),
            'accreditation' => __('forms.accreditation'),
            'license' => __('forms.licenses'),
            'archivation' => __('forms.information'),
            'information' => __('forms.information'),
            'significancy' => __('forms.complete'),
        ];

        try {
            $this->legalEntityForm->onEditValidate();
        } catch (ValidationException $err) {
            $key= array_key_first($err->errors());
            $error = $err->errors()[$key][0];

            if (str_contains($key, 'legalEntityForm.')) {
                $stepGroup = explode('.', str_replace('legalEntityForm.', '', $key));
                $_step = $stepGroup[0];
                $field = $stepGroup[count($stepGroup) - 1];

                $step = match($_step) {
                    'owner', 'accreditation', 'license' => $_step,
                    'significancy' => 'significancy',
                    'edrpou' => 'edrpou',
                    'email' => 'contact',
                    'website' => 'contact',
                    'phones' => 'contact',
                    'archive' => 'archivation',
                    'receiverFundsCode' => 'information',
                    'beneficiary' => 'information',
                    default => 'undefined'
                };
            } else if (str_contains($key, 'address.')) {
                $step = 'address';
            }

            if (!$step) {
                $step = 'undefined';
            }

            $this->validationErrorStep = $stepNames[$step];

            $this->dispatchErrorMessage("[$step : $field] $error");

            return false;
        }

        return true;
    }

    public function createLegalEntity()
    {
        $this->stepSignificancy();

        // Validate All the data from the form
        if ($this->validationRequest()) {
            $result = $this->signLegalEntity();

            // Handle successful API response
            try {
                $this->handleSuccessResponse($result['response'], $result['request']);
            } catch(Exception $err) {
                // Dispatch error message for possible errors
                $this->dispatchErrorMessage($err->getMessage());
            }
        }
    }

    public function render()
    {
        return view('livewire.legal-entity.create-legal-entity', [
            'activeStep' => $this->steps['index'],
            'currentStep' => $this->steps['index'],
            'isEdit' => false
        ]);
    }
}
