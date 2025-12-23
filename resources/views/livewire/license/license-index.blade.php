@use('App\Enums\License\Type')
@use('App\Models\License')

<div>
    <x-header-navigation x-data="{ showFilter: false }" title="{{ __('forms.licenses') }}">
        <div class="flex flex-col">
            <div class="flex flex-wrap items-end justify-between gap-4 max-w-6xl">
                <div class="flex items-end gap-4"></div>
                <div class="ml-auto flex items-center gap-6 self-start -mt-9 translate-x-4">
                    @can('create', License::class)
                        <a href="{{ route('license.create', [legalEntity()]) }}"
                           class="button-primary flex items-center gap-2"
                        >
                            @icon('plus', 'w-4 h-4')
                            {{ __('licenses.create') }}
                        </a>
                    @endcan

                    @can('sync', License::class)
                        <button wire:click="sync" class="button-sync flex items-center gap-2">
                            @icon('refresh', 'w-4 h-4')
                            {{ __('forms.synchronise_with_eHealth') }}
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </x-header-navigation>

    <div class="flow-root mt-4 shift-content pl-3.5" wire:key="{{ time() }}">
        <div class="max-w-screen-xl">
            @if($licenses->isNotEmpty())
                <div class="relative shadow-md sm:rounded-lg">
                    <table class="w-full min-w-[1100px] text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('licenses.type') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('licenses.active_from_date_label') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('licenses.expiry_date_label') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('licenses.activity') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('licenses.kind') }}</th>
                            <th scope="col" class="px-6 py-3 text-center">{{ __('forms.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($licenses as $license)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <td class="px-6 py-4 break-words whitespace-normal align-top">
                                    {{ $license->type->label() }}
                                </td>
                                <td class="px-6 py-4 break-words whitespace-normal align-top">
                                    {{ $license->activeFromDate }}
                                </td>
                                <td class="px-6 py-4 break-words whitespace-normal align-top">
                                    {{ $license->expiryDate }}
                                </td>
                                <td class="px-6 py-4 break-words whitespace-normal align-top">
                                    {{ $license->whatLicensed }}
                                </td>
                                <td class="px-6 py-4 break-words whitespace-normal align-top">
                                    @if($license->isPrimary)
                                        <span class="badge-green">{{ __('licenses.primary') }}</span>
                                    @else
                                        <span class="badge-yellow">{{ __('licenses.not_primary') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center align-top">
                                    @if($license->isPrimary)
                                        @can('view', $license)
                                            <a href="{{ route('license.view', [legalEntity(), $license->id]) }}"
                                               class="text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white"
                                               title="{{ __('forms.view') }}"
                                            >
                                                @icon('eye', 'w-5 h-5 svg-hover-action')
                                            </a>
                                        @endcan
                                    @else
                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                            <button @click="open = !open"
                                                    @click.outside="open = false"
                                                    class="cursor-pointer text-gray-500 hover:text-gray-800 dark:hover:text-white focus:outline-none"
                                            >
                                                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"
                                                    />
                                                </svg>
                                            </button>

                                            <div x-show="open"
                                                 x-cloak
                                                 x-transition
                                                 class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            >
                                                <div class="py-1">
                                                    @can('view', $license)
                                                        <a href="{{ route('license.view', [legalEntity(), $license->id]) }}"
                                                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                                        >
                                                            @icon('eye', 'w-5 h-5 svg-hover-action')
                                                            {{ __('forms.view') }}
                                                        </a>
                                                    @endcan

                                                    @can('update', $license)
                                                        <a href="{{ route('license.edit', [legalEntity(), $license->id]) }}"
                                                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                                        >
                                                            <svg class="w-4 h-4"
                                                                 fill="none"
                                                                 stroke="currentColor"
                                                                 viewBox="0 0 24 24"
                                                            >
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"
                                                                />
                                                            </svg>
                                                            {{ __('forms.update') }}
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $licenses->links() }}
                </div>
            @else
                <fieldset class="fieldset !mx-auto mt-8">
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

        <x-messages />
        <x-forms.loading />
    </div>
</div>
