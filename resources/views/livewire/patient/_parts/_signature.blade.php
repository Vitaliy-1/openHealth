<div
    class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        {{ __('forms.signature') }}
    </h5>

    <div class="max-w-3xl">
        <div id="printable-content">
            <div class="mb-3">
                <div>Ви, як медичний працівник закладу охорони здоров’я:</div>
                <ul style="list-style-type: none">
                    <li>
                        - підтверджуєте, що пацієнта як особу ідентифіковано;
                    </li>
                    <li>
                        - підтверджуєте, що повідомили пацієнту або його представнику мету та підстави обробки його
                        персональних даних.
                    </li>
                </ul>
            </div>

            <div id="reminder-section" class="mb-3">
                <h5>ПАМ’ЯТКА ПАЦІЄНТУ</h5>
                <div>
                    Надаючи код або документи особа чи її представник:
                </div>
                <ul style="list-style-type: none">
                    <li>
                        - надає згоду медичному працівнику закладу охорони здоров’я на обробку персональних даних
                        пацієнта, для якого створюється запис в реєстрі пацієнтів Електронної системи охорони здоров’я;
                    </li>
                    <li>
                        - надає згоду медичному працівнику закладу охорони здоров’я створити та при необхідності оновити
                        запис про пацієнта у електронній системі охорони здоров’я від імені особи або її представника.
                    </li>
                </ul>
            </div>
        </div>

        <button onclick="printContent('printable-content')" class="mb-3 underline">
            Роздрукувати пам'ятку для ознайомлення пацієнтом
        </button>

        <x-forms.form-row class="flex-col">
            <x-forms.form-group class="xl:w-1/2 flex items-center gap-3">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="isInformed">
                        {{ __("Інформація з пам'ятки повідомлена пацієнту") }}
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-checkbox class="default-checkbox mb-2"
                                wire:model.live="isInformed"
                                id="isInformed"
                    />
                </x-slot>
            </x-forms.form-group>
        </x-forms.form-row>

        <x-forms.form-row>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="confirmation_code" class="default-label">
                        {{ __('Код підтвердження') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    @if($isApproved)
                        <span class="text-green-500">Підтверджено</span>
                    @else
                        <x-forms.input class="default-input"
                                       wire:model="patientRequest.confirmation_code"
                                       type="text"
                                       id="confirmation_code"
                                       maxlength="4"
                        />
                    @endif
                </x-slot>

                @error('patientRequest.confirmation_code')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>

            <div class="xl:w-1/4 flex">
                <button wire:click="approvePerson('confirmation_code')" type="button"
                        class="btn-primary" {{ $isInformed ? '' : 'disabled' }}>
                    {{ __('Відправити на затвердження') }}
                </button>
            </div>
        </x-forms.form-row>

        @if ($isApproved)
            <div class="xl:w-1/4">
                <button wire:click="create('signed_content')" type="button" class="default-button">
                    {{__('Підписати КЕПом')}}
                </button>
            </div>
        @endif

        @if($showModal === 'signed_content')
            @include('livewire.patient._parts.modals._modal_signed_content')
        @endif
    </div>
</div>

<script>
    function printContent(elementId) {
        const content = document.getElementById(elementId).innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.open();
        printWindow.document.write(`
        <html lang="uk">
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
        <body onload="window.print(); window.close();">
            ${content}
        </body>
        </html>
    `);
        printWindow.document.close();
    }
</script>
