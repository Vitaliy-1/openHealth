<?php

declare(strict_types=1);

namespace App\Livewire\DiagnosticReport;

use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Models\MedicalEvents\Sql\DiagnosticReport;
use App\Repositories\MedicalEvents\Repository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class DiagnosticReportCreate extends DiagnosticReportComponent
{
    public function render(): View
    {
        return view('livewire.diagnostic-report.create');
    }

    /**
     * Validate and save data.
     *
     * @param  array  $data
     * @return void
     */
    public function save(array $data): void
    {
        if (!Auth::user()?->can('store', DiagnosticReport::class)) {
            $this->flashPolicyError();

            return;
        }

        $this->form->diagnosticReports = $this->pruneReferralData($data);
        $formattedData = Repository::diagnosticReport()->formatRequest($this->form->diagnosticReports);

        if (!$this->validateFormatted($formattedData)) {
            return;
        }

        try {
            $this->storeValidatedData($formattedData);
        } catch (Throwable) {
            $this->flashGeneralError();
        }
    }

    /**
     * Submit encrypted data.
     *
     * @param  array  $data
     * @return void
     * @throws ApiException
     */
    public function sign(array $data): void
    {
        if (!Auth::user()?->can('store', DiagnosticReport::class)) {
            $this->flashPolicyError();

            return;
        }

        $this->form->diagnosticReports = $this->pruneReferralData($data);
        $formattedData = Repository::diagnosticReport()->formatRequest($this->form->diagnosticReports);

        if (!$this->validateFormatted($formattedData)) {
            return;
        }

        try {
            $this->storeValidatedData($formattedData);
        } catch (Throwable) {
            $this->flashGeneralError();
        }

        $base64EncryptedData = $this->sendEncryptedData(
            $this->convertArrayKeysToSnakeCase($formattedData),
            Auth::user()->tax_id
        );

        PatientApi::submitDiagnosticReportPackage($this->patientUuid, ['signed_data' => $base64EncryptedData]);
    }

    /**
     * Unset unnecessary data based on referral type.
     *
     * @param  array  $data
     * @return array
     */
    protected function pruneReferralData(array $data): array
    {
        if ($data['referralType'] === 'electronic' || $data['referralType'] === '') {
            unset($data['paperReferral']);
        }

        if ($data['referralType'] === 'paper' || $data['referralType'] === '') {
            unset($data['basedOn']);
        }

        return $data;
    }

    /**
     * Validate formatted data.
     *
     * @param  array  $formattedData
     * @return bool
     */
    protected function validateFormatted(array $formattedData): bool
    {
        try {
            $this->form->validateForm('diagnosticReport', $formattedData);

            return true;
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);

            return false;
        }
    }

    /**
     * Store validated formatted data into DB.
     *
     * @param  array  $formattedData
     * @return void
     * @throws Throwable
     */
    protected function storeValidatedData(array $formattedData): void
    {
        DB::transaction(static function () use ($formattedData) {
            Repository::diagnosticReport()->store($formattedData['diagnosticReports']);
        });
    }
}
