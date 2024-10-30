<?php

declare(strict_types=1);

namespace App\Livewire\Patient;

use App\Livewire\Patient\Forms\PatientFormRequest;
use App\Models\Person;
use App\Traits\FormTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PatientForm extends Component
{
    use FormTrait;

    private const string CACHE_PREFIX = 'register_patient_form';

    public string $mode = 'create';
    public PatientFormRequest $patient_request;
    public Person $patient;
    public string $request_id;
    public string $patient_id;
    protected string $patientCacheKey;
    public mixed $key_property;
    public bool $tax_id_missing = false;
    public ?array $dictionaries_field = [
        'GENDER',
        'DOCUMENT_TYPE',
    ];

    public function boot(): void
    {
        $this->patientCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->legalEntity->uuid;
    }

    public function mount(Request $request, $id = ''): void
    {
        if ($request->has('store_id')) {
            $this->request_id = $request->input('store_id');
        }

        if (!empty($id)) {
            $this->patient_id = $id;
        }

        $this->getPatient();
        $this->getDictionary();
    }

    public function render()
    {
        return view('livewire.patient.patient-form');
    }

    public function create(string $model): void
    {
        $this->mode = 'create';
        $this->patient_request->{$model} = [];
        $this->openModal($model);
        $this->getPatient();
    }

    /**
     * @throws ValidationException
     */
    public function store(string $model): void
    {
        $this->patient_request->rulesForModelValidate($model);

        $this->resetErrorBag();

        if (isset($this->request_id)) {
            $this->storeCachePatient($model);
        }

        $this->closeModalModel();

        $this->dispatch('flashMessage', ['message' => __('Інформацію успішно оновлено'), 'type' => 'success']);

        $this->getPatient();
    }

    public function storeCachePatient(string $model): void
    {
        $cacheData = [];

        if ($this->hasCache()) {
            $cacheData = $this->getCache();
        }

        if ($model === 'patient') {
            $cacheData[$this->request_id][$model] = $this->patient_request->{$model};
        } else {
            $cacheData[$this->request_id][$model][] = $this->patient_request->{$model};
        }

        $this->putCache($cacheData);
    }

    public function getPatient(): void
    {
        if (isset($this->request_id) && $this->hasCache()) {
            $patientData = $this->getCache();

            if (isset($patientData[$this->request_id])) {
                $this->patient = (new Person())->forceFill($patientData[$this->request_id]);

                if (!empty($this->patient->patient)) {
                    $this->patient_request->fill(
                        [
                            'patient' => $this->patient->patient,
                            'documents' => $this->patient->documents ?? [],
                        ]
                    );
                }
            }
        }
    }

    public function edit(string $model, $key_property = ''): void
    {
        $this->key_property = $key_property;
        $this->mode = 'edit';
        $this->openModal($model);

        if (isset($this->request_id)) {
            $this->editCacheEmployee($model, $key_property);
        }
    }

    private function editCacheEmployee(string $model, $key_property = ''): void
    {
        $cacheData = $this->getCache();

        if (empty($key_property) && $key_property !== 0) {
            $this->patient_request->{$model} = $cacheData[$this->request_id][$model];
        } else {
            $this->patient_request->{$model} = $cacheData[$this->request_id][$model][$key_property];
        }
    }

    /**
     * @throws ValidationException
     */
    public function update(string $model, int $key_property): void
    {
        $this->patient_request->rulesForModelValidate($model);
        $this->resetErrorBag();

        if (isset($this->request_id)) {
            $this->updateCachePatient($model, $key_property);
        }

        $this->closeModalModel($model);
    }

    public function updateCachePatient(string $model, int $key_property): void
    {
        if ($this->hasCache()) {
            $cacheData = $this->getCache();
            $cacheData[$this->request_id][$model][$key_property] = $this->patient_request->{$model};

            $this->putCache($cacheData);
        }
    }

    public function closeModalModel($model = null): void
    {
        if (!empty($model)) {
            $this->patient_request->{$model} = [];
        }

        $this->closeModal();
        $this->getPatient();
    }

    private function hasCache(): bool
    {
        return Cache::has($this->patientCacheKey);
    }

    private function getCache(): mixed
    {
        return Cache::get($this->patientCacheKey, []);
    }

    private function putCache($cacheData): void
    {
        Cache::put($this->patientCacheKey, $cacheData, now()->days(90));
    }
}
