<x-dialog-modal maxWidth="3xl" wire:model="showModal">
    <x-slot name="title">
        {{ __('patients.leaflet') }}
    </x-slot>

    <x-slot name="content">
        <div
            x-data="{
                printContent() {
                    const content = document.getElementById('printable-content').innerHTML;
                    const printWindow = window.open('', '_blank');
                    printWindow.document.open();
                    printWindow.document.write(`
                    <html lang='uk'>
                    <head>
                        <title>Друк пам'ятки</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                line-height: 1.5;
                                margin: 20px;
                            }
                        </style>
                    </head>
                    <body onload='window.print(); window.close();'>
                        ${content}
                    </body>
                    </html>
                `);
                    printWindow.document.close();
                }
            }"
        >
            <div class="mb-4.5 flex flex-col gap-6 xl:flex-container">
                @if(!empty($leafletContent))
                    <div id="printable-content">
                        {!! $leafletContent !!}
                    </div>
                @endif

                <button @click="printContent" class="mb-6 underline font-medium text-sm">
                    {{ __('patients.print_leaflet_for_patient') }}
                </button>

                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center">
                    <div class="xl:w-1/4 text-left">
                        <x-secondary-button wire:click="closeModalModel">
                            {{ __('forms.back') }}
                        </x-secondary-button>
                    </div>
                    <div class="xl:w-1/4 text-right">
                        <button wire:click="informAndCloseModal" type="button" class="default-button">
                            {{ __('forms.sign') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-dialog-modal>
