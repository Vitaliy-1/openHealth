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

        @if(!empty($uploadedDocuments))
            @foreach($uploadedDocuments as $key => $document)
                <div class="pb-4 flex items-center">
                    <div class="flex-grow">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">
                            {{ $document['type'] }} *
                        </label>
                        <div class="flex items-center gap-4">
                            <input
                                class="xl:w-1/2 block text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                id="file_input" type="file"
                                wire:model.live="patientRequest.uploadedDocuments.{{ $key }}.documentsRelationship">
                            <a type="button" href="#" class="text-green-700 hover:text-white border border-green-700
                                    hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300
                                    font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                    dark:border-green-500 dark:text-green-500 dark:hover:text-white
                                    dark:hover:bg-green-600 dark:focus:ring-green-800"
                               wire:click.prevent="uploadFile('uploadedDocuments', '{{ $document['type'] }}')"
                               wire:loading.attr="disabled"
                               wire:loading.class="opacity-50 cursor-not-allowed">
                                {{ __('Відправити') }}
                            </a>
                        </div>
                    </div>
                </div>

                @error('patientRequest.uploadedDocuments.documentsRelationship')
                <div class="text-red-500 text-sm mb-2">
                    {{ $message }}
                </div>
                @enderror

            @endforeach
        @endif

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
                                       wire:model="patientRequest.confirmationCode"
                                       type="text"
                                       id="confirmation_code"
                                       maxlength="4"
                        />
                    @endif
                </x-slot>

                @error('patientRequest.confirmationCode')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>

            <!-- Resend SMS button -->
            <div class="xl:w-1/4 flex items-end">
                <button
                    type="button"
                    wire:click="resendSms"
                    x-data="{
                    cooldown: @entangle('resendCooldown'),
                    startCooldown() {
                        if (this.cooldown > 0) {
                            const interval = setInterval(() => {
                                if (this.cooldown > 0) {
                                    this.cooldown--;
                                } else {
                                    clearInterval(interval);
                                }
                            }, 1000);
                        }
                    },
                }"
                    x-init="startCooldown()"
                    x-effect="startCooldown()"
                    x-bind:disabled="cooldown > 0"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:hover:bg-gray-300"
                >
                    <svg
                        x-show="cooldown > 0"
                        x-cloak
                        aria-hidden="true"
                        class="w-4 h-4 mr-2 text-gray-200 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C3.582 0 0 3.582 0 8h4zm2 5.291V20c2.485 0 4.68-1.122 6-2.905l-1.609-.692A7.992 7.992 0 016 17.291z"></path>
                    </svg>
                    <span
                        x-text="cooldown > 0 ? `Повторна відправка коду через ${cooldown} сек.` : 'Відправити ще раз'"></span>
                </button>
            </div>

            <div class="xl:w-1/4 flex">
                <button wire:click="approvePerson('confirmationCode')" type="button"
                        class="btn-primary" {{ $isInformed ? '' : 'disabled' }}>
                    {{ __('Відправити на затвердження') }}
                </button>
            </div>
        </x-forms.form-row>

        @if($isApproved)
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
