<?php

namespace App\Livewire\Contract;

use App\Classes\Cipher\Api\CipherApi;
use App\Livewire\Contract\Forms\Api\ContractRequestApi;
use App\Livewire\Contract\Forms\ContractFormRequest;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use App\Models\Contract;
use App\Models\Division;
use App\Models\HealthcareService;
use App\Models\LegalEntity;
use App\Traits\FormTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class ContractForm extends Component
{
    use FormTrait, WithFileUploads;


    const CACHE_PREFIX = 'register_contract_form';
    public ?array $dictionaries_field = [
        'CONTRACT_TYPE',
        'CAPITATION_CONTRACT_CONSENT_TEXT',
    ];

    public ?LegalEntity $legalEntity;

    public ?Collection $divisions;
    public ?Collection $healthcareServices;

    public ContractFormRequest $contract_request;

    public  ? array $getCertificateAuthority;

    public array $legalEntityApi = [];

    public array $external_contractors = [];
    public string $external_contractor_key = '';
    public string $legalEntity_search = '';
    public string $contractCacheKey;
    public string $knedp = '';
    public $keyContainerUpload;

    public string $password = '';

    public function boot()
    {
        $this->contractCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->legalEntity->uuid;
    }

    public function mount($id = '')
    {
        if ($id !== '') {
            $this->contract_request->previous_request_id = $id;
        }
        $this->getCertificateAuthority = (new CipherApi())->getCertificateAuthority();

        $this->getDictionary();
        $this->getLegalEntity();
    }

    public function getLegalEntity()
    {
        $this->legalEntity = auth()->user()->legalEntity;
        $this->getDivisions();
    }

    public function render()
    {
        return view('livewire.contract.contract-form');
    }

    public function getDivisions()
    {
        $this->divisions = $this->legalEntity->division->where('status', 'ACTIVE');
    }


    public function contractType()
    {
        return $this->legalEntity->contract_type;
    }


    public function getLegalEntityApi()
    {
        if (strlen($this->legalEntity_search) >= 7) {
            $this->legalEntityApi = LegalEntitiesRequestApi::getLegalEntities($this->legalEntity_search);
        }

    }


    public function addExternalContractors(): void
    {
        $this->validateExternalContractors();
        if ($this->external_contractor_key !== '') {
            $this->external_contractors[$this->external_contractor_key] = $this->contract_request->external_contractors;
        } else {
            $this->external_contractors[] = $this->contract_request->external_contractors;
        }
        $this->contract_request->external_contractors = [];
        $this->closeModal();
    }

    private function validateExternalContractors(): void
    {
        $this->contract_request->rulesForModelValidate('external_contractors');
    }

    private function resetExternalContractorKeyAndRequest(): void
    {
        $this->external_contractor_key = '';
        $this->contract_request->external_contractors = [];
    }


    public function closeModal(): void
    {
        $this->resetExternalContractorKeyAndRequest();
        $this->contract_request->external_contractors = [];
        $this->legalEntity_search = '';
        $this->showModal = false;
    }

    public function editExternalContractors($key): void
    {
        $this->external_contractor_key = $key;
        $this->contract_request->external_contractors = $this->external_contractors[$this->external_contractor_key];
        $this->legalEntity_search = $this->contract_request->external_contractors['legal_entity']['name'];
        $this->openModal();
    }

    public function openModalSigned(): void
    {
        $this->contract_request->rulesForModelValidate();
        $this->openModal('signed_content');
    }


    public function deleteExternalContractors($key): void
    {
        unset($this->external_contractors[$key]);
    }


    public function getHealthcareServices($id): void
    {
        $division = Division::find($id);
        $this->contract_request->external_contractors['divisions']['name'] = $division->name;
        $this->contract_request->external_contractors['divisions']['uuid'] = $division->uuid;
        $this->healthcareServices = $division
            ->healthcareService()
            ->get();

    }

    public function sendApiRequest()
    {
        dd($this->requestBuilder());

        $this->contract_request->rulesForModelValidate();
        $removeKeyEmpty = removeEmptyKeys($this->contract_request->toArray());
        $base64Data = (new CipherApi())->sendSession(
            json_encode($removeKeyEmpty),
            $this->password,
            $this->keyContainerUpload,
            $this->knedp
        );
        if (isset($base64Data['errors'])) {
            $this->dispatch('flashMessage', [
                'message' => $base64Data['errors'],
                'type'    => 'error'
            ]);
            return;
        }

        $data = [
            'signed_content'          => $base64Data,
            'signed_content_encoding' => 'base64',
        ];

        $contract_response = ContractRequestApi::contractRequestApi($data, Cache::get($this->contractCacheKey));
        $contract = new Contract($contract_response);
        $contract->uuid = $contract_response['id'];
        $contract->contractor_legal_entity_id = $contract_response['contractor_legal_entity']['id'];
        $contract->contractor_owner_id = $contract_response['contractor_owner']['id'];
        $this->legalEntity->contract()->save($contract);
        Cache::forget($this->contractCacheKey);
        return redirect()->route('contract.index');
    }


    public function requestBuilder()
    {

        dd($this->legalEntity);
        $data = $this->contract_request->toArray();
        $data['additional_document_md5'] = md5_file($this->contract_request->additional_document_md5->getRealPath());
        $data['statute_md5'] = md5_file($this->contract_request->statute_md5->getRealPath());
        $data['end_date'] = Carbon::parse($this->contract_request->end_date)->format('Y-m-d');
        $data['start_date'] = Carbon::parse($this->contract_request->start_date)->format('Y-m-d');
        $data['contractor_owner_id'] = $this->legalEntity->getOwner()->uuid;

        return $data;
    }



}
