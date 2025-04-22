<?php

namespace App\Livewire\Employee;

use App\Models\Employee\EmployeeRequest;
use App\Repositories\EmployeeRepository;
use App\Services\LegalEntityContext;
use App\Services\SignatureService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\WithFileUploads;
use Illuminate\Validation\ValidationException;

class EmployeeCreate extends EmployeeComponent
{
    use WithFileUploads;

    public Forms\EmployeeForm $form;

    protected SignatureService $signatureService;

    /**
     * @var array List of certificate authorities.
     */
    public array $certificateAuthorities = [];

    /**
     * The currently active EmployeeRequest draft.
     * @var EmployeeRequest|null
     */
    public ?EmployeeRequest $currentEmployeeRequest = null;

    /**
     * Property to control the visibility of the KEP signature block.
     * @var bool
     */
    public bool $showSignatureBlock = false;

    /**
     * Bootstrap the component, injecting dependencies.
     * @param EmployeeRepository $employeeRepository
     * @param LegalEntityContext $legalEntityContext
     * @return void
     */
    public function boot(EmployeeRepository $employeeRepository, LegalEntityContext $legalEntityContext): void
    {
        parent::boot($employeeRepository, $legalEntityContext);
        $this->signatureService = app(SignatureService::class);
        $this->certificateAuthorities = $this->signatureService->getCertificateAuthorities();
    }

    public function save(): void
    {
        try {
            $formData = $this->form->validated();

            $formData['uuid'] = null;

            $formData['status'] = 'NEW';

            $formData['start_date'] = $formData['party']['startDate'] ?? Carbon::now()->toDateString();
            unset($formData['party']['startDate']);

            $formData['end_date'] = $formData['party']['endDate'] ?? null;
            if (isset($formData['party']['endDate'])) {
                unset($formData['party']['endDate']);
            }

            $formData['employee_type'] = $formData['party']['employeeType'] ?? null;
            unset($formData['party']['employeeType']);

            $formData['position'] = $formData['party']['position'] ?? null;
            unset($formData['party']['position']);

            $formData['user_id'] = auth()->id();
            $formData['legal_entity_id'] = $this->legalEntityContext->id();
            $formData['legal_entity_uuid'] = $this->legalEntityContext->uuid();

            if (isset($formData['doctor']['divisionUuid'])) {
                $formData['division_uuid'] = $formData['doctor']['divisionUuid'];
                unset($formData['doctor']['divisionUuid']);
            } else {
                $formData['division_uuid'] = null;
            }

            app(EmployeeRepository::class)->saveEmployeeData(
                $formData,
                $this->legalEntityContext->current()
            );

            session()->flash('success', __('Employee request saved successfully.'));
            $this->showSignatureBlock = true;

        } catch (ValidationException $e) {
            $this->dispatch('employee-form-failed');
            Log::error('Validation Error in EmployeeCreate::save(): ' . json_encode($e->errors(), JSON_THROW_ON_ERROR));
            session()->flash('error', __('Validation failed. Please check the form.'));
            throw $e;
        } catch (Exception $e) {
            $this->dispatch('employee-form-failed');
            Log::error('Critical Error in EmployeeCreate::save(): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            session()->flash('error', __('Failed to save employee. An unexpected error occurred.'));
            $this->showSignatureBlock = false;
        }
    }

    /**
     * Render the component view.
     * @return View
     */
    public function render(): View
    {
        $pageTitle = __('forms.add_employee');
        return view('livewire.employee.employee-create', compact('pageTitle'));
    }
}
