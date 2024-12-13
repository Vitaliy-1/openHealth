<?php

namespace App\Livewire\Patient;

use App\Models\LegalEntity;
use App\Traits\InteractsWithCache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PatientIndex extends Component
{
    use InteractsWithCache;

    private const string CACHE_PREFIX = 'register_patient_form';

    /**
     * @var object|null
     */
    public ?object $patients = null;

    /**
     * @var object|null
     */
    public ?object $patient_show = null;
    protected string $patientCacheKey;
    public int $storeId = 0;
    private LegalEntity $legalEntity;
    public string $firstName = '';
    public string $lastName = '';
    public string $birthDate = '';
    public string $secondName = '';
    public string $email = '';
    public string $ipn = '';
    public string $phone = '';
    public string $birthCertificate = '';

    /**
     * Search for patient in eHealth
     */
    public function search(): void
    {
    }

    public function boot(): void
    {
        $this->patientCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->legalEntity->uuid;

        $this->legalEntity = Auth::user()->legalEntity;
    }

    public function mount(): void
    {
        $this->getLastStoreId();
    }

    public function getLastStoreId(): void
    {
        $cacheData = $this->getCache($this->patientCacheKey);

        if (empty($cacheData) || !is_array($cacheData) || !$this->hasCache($this->patientCacheKey)) {
            $this->storeId = 0;
            return;
        }

        $this->storeId = array_key_last($cacheData) + 1;
    }

    public function render(): View
    {
        return view('livewire.patient.index');
    }
}
