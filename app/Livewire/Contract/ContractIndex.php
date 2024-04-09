<?php

namespace App\Livewire\Contract;

use App\Livewire\Contract\Forms\Api\ContractRequestApi;
use App\Models\Employee;
use App\Traits\FormTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
class ContractIndex extends Component
{
    use FormTrait;

    const CACHE_PREFIX = 'register_contract_form';

    public ?array $tableHeaders;

    public ?array $dictionaries_field = [
        'CONTRACT_TYPE',
    ];

    #[Validate('required')]
    public string $contract_type;

    protected string $contractCacheKey;
    /**
     * @var true
     */
    public bool $hasInitContract = true;


    public function boot(): void
    {
        $this->contractCacheKey = self::CACHE_PREFIX . '-'. Auth::user()->legalEntity->uuid;
    }


    public function mount()
    {
        $this->tableHeaders();
        $this->getDictionary();
        $this->hasInitContract();
//        dd(Cache::get($this->contractCacheKey));
    }

    public function tableHeaders()
    {
        $this->tableHeaders = [
            'Contract Name',
            'Description',
            'Actions',
        ];
    }

    public function render()
    {
        return view('livewire.contract.contract-index');
    }

    public function createRequest()
    {
        if (Cache::has($this->contractCacheKey)){
            return redirect()->route('contract.form');
        }
        $this->validate();

        $initContractRequestApi = ContractRequestApi::initContractRequestApi($this->contract_type);
        if (!empty($initContractRequestApi)){
           Cache::put($this->contractCacheKey, $initContractRequestApi);
        }
        return redirect()->route('contract.form');

    }

    public function hasInitContract()
    {
        if (Cache::has($this->contractCacheKey)){
            $this->hasInitContract = false;
        }
    }



}
