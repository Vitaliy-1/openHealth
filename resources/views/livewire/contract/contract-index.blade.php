@php
    use App\Enums\Contract\Type;
    use App\Models\{LegalEntity, ContractRequest};

    // Dynamic route generation based on Legal Entity Type
    $route = '#';
    if (legalEntity()->type->name === LegalEntity::TYPE_PHARMACY) {
        $route = route('contract-reimbursement.create', legalEntity());
    } elseif (legalEntity()->type->name === LegalEntity::TYPE_MSP) {
        $route = route('contract-capitation.create', legalEntity());
    }
@endphp

<div>
    <x-messages/>
    <x-forms.loading/>

    <x-header-navigation class="items-start">
        <x-slot name="title">{{ __('forms.contracts') }}</x-slot>

        <div class="mt-3 ml-0 flex flex-col sm:flex-row sm:flex-wrap gap-2 self-start">
            @can('create', ContractRequest::class)
                {{-- Button now uses the dynamic $route variable --}}
                <a href="{{ $route }}" class="button-primary flex items-center gap-2" wire:navigate>
                    @icon('plus', 'w-4 h-4')
                    {{ __('contracts.new') }}
                </a>
            @endcan

            @can('sync', ContractRequest::class)
                <button wire:click="sync" type="button" class="button-sync flex items-center gap-2 whitespace-nowrap">
                    @icon('refresh', 'w-4 h-4')
                    {{ __('forms.synchronise_with_eHealth') }}
                </button>
            @endcan
        </div>

        <x-slot name="navigation">
            <div class="flex flex-col -my-4" x-data="{ showFilter: false }">
                <div class="flex mb-4 flex-col lg:flex-row items-stretch lg:items-end gap-2 lg:gap-4 w-full">
                    <div class="w-full lg:w-96">

                        {{-- Filters --}}
                        <div class="form-group group"
                             x-data="{ open: false, selectedTypes: $wire.entangle('typeFilter') }"
                        >
                            <label for="typeFilter" class="label">{{ __('forms.type') }}</label>
                            <div class="relative">
                                <input type="text"
                                       id="typeFilter"
                                       class="input peer w-full cursor-pointer text-gray-500 dark:text-gray-400"
                                       placeholder="{{ __('forms.select') }}"
                                       @click="open = !open"
                                       :value="selectedTypes.length ? selectedTypes.map(status => {
                                           if (status === 'DRAFT') return '{{ __('forms.status.drafts') }}';
                                           if (status === 'CONTRACT_REQUESTS') return '{{ __('contracts.status.requests') }}';
                                           if (status === 'CONTRACTS') return '{{ __('forms.contracts') }}';

                                           return status;
                                       }).join(', ') : ''"
                                       readonly
                                />
                                @icon('chevron-down', 'w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 pointer-events-none')

                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg"
                                >
                                    <ul class="py-2 px-3 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                                        @foreach(Type::options() as $value => $label)
                                            <li>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox"
                                                           value="{{ $value }}"
                                                           wire:model="typeFilter"
                                                           class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent"
                                                    />
                                                    <span>{{ $label }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-9 mt-6 flex flex-col sm:flex-row gap-2 w-full">
                    <button wire:click="search" type="submit" class="flex items-center gap-2 button-primary">
                        @icon('search', 'w-4 h-4')
                        <span>{{ __('forms.search') }}</span>
                    </button>
                    <button type="button" wire:click="resetFilters" class="button-primary-outline-red">
                        {{ __('forms.reset_all_filters') }}
                    </button>
                </div>
            </div>
        </x-slot>
    </x-header-navigation>

    <div class="flow-root mt-8 shift-content pl-3.5"
         wire:key="contracts-table-page-{{ $contracts->total() }}-{{ $contracts->currentPage() }}"
    >
        <div class="max-w-screen-xl">
            <div class="relative shadow-md sm:rounded-lg">
                @if($contracts->isNotEmpty())
                    <table
                        class="w-full table-fixed text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3 w-[10%] text-left">{{ __('forms.type') }}</th>
                            <th class="px-6 py-3 w-[20%] text-left">№</th>
                            <th class="px-6 py-3 w-[15%] text-left">{{ __('contracts.start_date') }}</th>
                            <th class="px-6 py-3 w-[15%] text-left">{{ __('contracts.end_date') }}</th>
                            <th class="px-6 py-3 w-[20%] text-left">{{ __('forms.status.label') }}</th>
                            <th class="px-6 py-3 w-[6%] text-center">{{ __('forms.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($contracts as $contract)
                            <tr wire:key="contract-{{ $contract->id }}"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150"
                            >
                                {{-- TYPE --}}
                                <td class="td-input align-middle text-sm text-gray-900 dark:text-white">
                                    <div class="flex flex-col">
                <span class="font-medium">
                    @if($contract->type === 'REIMBURSEMENT')
                        {{ __('Реімбурсація') }}
                    @elseif($contract->type === 'CAPITATION')
                        {{ __('ПМД (Капітація)') }}
                    @else
                        {{ $contract->type }}
                    @endif
                </span>
                                        <span class="text-xs text-gray-500">
                    {{ $contract->id_form ?? '' }}
                </span>
                                    </div>
                                </td>

                                {{-- NUMBER --}}
                                <td class="td-input align-middle font-medium text-gray-900 dark:text-white">
                                    @if($contract->contract_number)
                                        {{ $contract->contract_number }}
                                    @else
                                        <span class="text-gray-400 italic">{{ __('Чернетка') }}</span>
                                    @endif
                                </td>

                                {{-- START DATE --}}
                                <td class="td-input align-middle">
                                    {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d.m.Y') : '---' }}
                                </td>

                                {{-- END DATE --}}
                                <td class="td-input align-middle">
                                    {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d.m.Y') : '---' }}
                                </td>

                                {{-- STATUS --}}
                                <td class="td-input align-middle">
                                    <div class="flex flex-col items-start gap-1">
                                        {{-- Ensure your Enum has label() and color() methods --}}
                                        <span
                                            class="{{ $contract->status->color() }} px-2.5 py-0.5 rounded text-xs font-medium">
                    {{ $contract->status->label() }}
                </span>

                                        @if($contract->status_reason)
                                            <div class="flex items-center gap-1 text-xs text-red-500 max-w-[150px]"
                                                 title="{{ $contract->status_reason }}">
                                                @icon('exclamation-circle', 'w-3 h-3 flex-shrink-0')
                                                <span class="truncate">{{ $contract->status_reason }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- ACTIONS --}}
                                <td class="td-input align-middle text-center px-2">
                                    @include('livewire.contract.parts.actions', ['contract' => $contract])
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                @else
                    <fieldset class="fieldset max-w-[1120px]">
                        <legend class="legend relative -top-5">@icon('nothing-found', 'w-28 h-28')</legend>
                        <div class="p-4 rounded-lg bg-blue-100 flex items-start mb-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    @icon('alert-circle', 'w-5 h-5 text-blue-500 mr-3 mt-1')
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-blue-800">
                                        {{ __('forms.nothing_found') }}
                                    </p>
                                    <p class="text-sm text-blue-600">
                                        {{ __('forms.changing_search_parameters') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                @endif
            </div>

            <div class="mt-8 pl-3.5 pb-8 lg:pl-8 2xl:pl-5">
                {{ $contracts->links() }}
            </div>
        </div>
    </div>
</div>
