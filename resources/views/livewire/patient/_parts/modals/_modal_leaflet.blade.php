<x-dialog-modal maxWidth="3xl" class="w-3 h-full" wire:model.live="showModal">
    <x-slot name="title">
        {{__("Пам’ятка")}}
    </x-slot>

    <x-slot name="content">
        <div class="mb-4.5 flex flex-col gap-6 xl:flex-container">

            @if(!empty($leafletContent))
                <div id="printable-content">
                    {!! $leafletContent !!}
                </div>
            @endif

            <button onclick="printContent('printable-content')" class="mb-6 underline font-medium text-sm">
                {{ __("Роздрукувати пам'ятку для ознайомлення пацієнтом") }}
            </button>

            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center">
                <div class="xl:w-1/4 text-left">
                    <x-secondary-button wire:click="closeModalModel">
                        {{ __('forms.back') }}
                    </x-secondary-button>
                </div>
                <div class="xl:w-1/4 text-right">
                    <button wire:click="informAndCloseModal" type="button" class="default-button">
                        {{ __('Підписати') }}
                    </button>
                </div>
            </div>
        </div>
    </x-slot>
</x-dialog-modal>
