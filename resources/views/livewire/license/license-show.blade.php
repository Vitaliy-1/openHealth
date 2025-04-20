<div>
    <x-section-navigation x-data="{ showFilter: false }" class=''>
        <x-slot name='title'>{{ __('Деталі Ліцензії') }}</x-slot>
        <x-slot name='description'>{{ __('Деталі Ліцензії') }}</x-slot>
    </x-section-navigation>

    <div class='inline-block min-w-full align-middle p-6 bg-white'>
        <div class='w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700'>
            <div class='items-center border-stroke px-7 py-4 dark:border-strokedark'>
                <div class='mb-4'>
                    <p>
                        <strong>{{ __('forms.type') }}:</strong> {{ $license->type_value }}
                    </p>
                </div>
                <div class='mb-4'>
                    <p>
                        <strong>{{ __('Ким видано ліцензію') }}:</strong> {{ $license->issued_by }}
                    </p>
                </div>
                <div class='mb-4'>
                    <p>
                        <strong>{{ __('Дата видачі ліцензії') }}:</strong> {{ $license->issued_date }}
                    </p>
                </div>
                <div class='mb-4'>
                    <p>
                        <strong>{{ __('Дата початку дії ліцензії') }}:</strong> {{ $license->active_from_date }}
                    </p>
                </div>
                <div class='mb-4'>
                    <p>
                        <strong>{{ __('Номер наказу') }}:</strong> {{ $license->order_no }}
                    </p>
                </div>
                <div class='mb-4'>
                    <p>
                        <strong>{{ __('Cерія та/або номер ліцензії') }}:</strong> {{ $license->license_number }}
                    </p>
                </div>
                @if ($license->expiry_date)
                    <div class='mb-4'>
                        <p>
                            <strong>{{ __('Дата завершення дії ліцензії') }}:</strong> {{ $license->expiry_date }}
                        </p>
                    </div>
                @endif
                <div class='mb-4'>
                    <p>
                        <strong>{{ __('Напрям діяльності, що ліцензовано') }}:</strong> {{ $license->what_licensed }}
                    </p>
                </div>
                <div class='mb-4'>
                    <p>
                        <strong>{{ __('Основна ліцензія') }}:</strong> {{ $license->is_primary === 1 ? __('forms.yes') : __('forms.no') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class='flex flex-start border-stroke px-7 py-2 mt-4'>
        <x-secondary-button wire:click='back'>
            {{ __('Назад') }}
        </x-secondary-button>
    </div>
</div>
