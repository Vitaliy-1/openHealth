<?php

namespace App\Livewire\Contract;

use App\Livewire\Contract\Forms\Api\ContractRequestApi;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Traits\FormTrait;
use Illuminate\Support\Collection;
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

    public ?LegalEntity $legalEntity;

    public ?Contract $contract;
    public function getLegalEntity(): void
    {
        $this->legalEntity = auth()->user()->legalEntity;
    }

    public function boot(): void
    {
        $this->contractCacheKey = self::CACHE_PREFIX . '-'. Auth::user()->legalEntity->uuid;
    }


    public function mount(): void
    {
        $this->tableHeaders();
        $this->getDictionary();
        $this->hasInitContract();
        $this->getLegalEntity();

//        dd(Cache::get($this->contractCacheKey));
    }

    public function tableHeaders(): void
    {
        $this->tableHeaders = [
            __('ID'),
            __('forms.number_contract'),
            __('forms.start_date'),
            __('forms.end_date'),
            __('forms.status'),
            __('forms.action'),
        ];
    }

    public function render()
    {
        $perPage = config('pagination.per_page');
        $contracts = $this->legalEntity->contract()->paginate($perPage);

        return view('livewire.contract.contract-index', compact('contracts'));
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

    public function hasInitContract(): void
    {
        if (Cache::has($this->contractCacheKey)){
            $this->hasInitContract = false;
        }
    }

    public function showContract($id):void
    {
        $this->contract = Contract::find($id);
        $this->openModal('show_contract');
    }

}
