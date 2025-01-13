<div class="w-full mb-8 p-10 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <div class="max-w-3xl">
        @if(!empty($patientRequest->uploadedDocuments))
            <h5 class="mb-8 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ __('Завантаження документів') }}
            </h5>

            @foreach($patientRequest->uploadedDocuments as $key => $document)
                <div class="pb-4 flex" wire:key="{{ $key }}">
                    <div class="flex-grow">
                        <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white"
                               for="file_input_{{ $key }}">
                            {{ __('patients.documents.' . Str::lower(Str::afterLast($document['type'], '.'))) }}
                        </label>
                        <div class="flex items-center gap-4">
                            <input
                                class="xl:w-1/2 block text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                id="file_input_{{ $key }}"
                                type="file"
                                wire:model.live="patientRequest.uploadedDocuments.{{ $key }}.documentsRelationship"
                            >

                            @if(isset($document['documentsRelationship']) && !$errors->has("patientRequest.uploadedDocuments.$key.documentsRelationship"))
                                @if(!isset($uploadedFiles[$key]) || $uploadedFiles[$key] === false)
                                    <button class="flex items-center gap-1"
                                            wire:click.prevent="deleteDocument({{ $key }})">
                                        <svg width="14" height="14">
                                            <use xlink:href="#svg-trash"></use>
                                        </svg>
                                        <span class="font-medium text-red-600 text-sm">{{ __('forms.delete') }}</span>
                                    </button>
                                @else
                                    <button class="flex items-center gap-1">
                                        <svg width="14" height="14">
                                            <use xlink:href="#svg-check-circle"></use>
                                        </svg>
                                        <span class="font-medium text-green-400 text-sm">{{ __('Відправлено') }}</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
                            {{ __('Розмір завантажуваного файлу не більше 10МБ у форматі jpeg') }}
                        </p>

                        @error("patientRequest.uploadedDocuments.$key.documentsRelationship")
                        <div class="mt-1">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </div>
                        @enderror
                    </div>
                </div>
            @endforeach

            @if(!$isUploaded)
                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.button-with-icon wire:click.prevent="sendFiles('uploadedDocuments')"
                                                  class="default-button flex-row-reverse mt-8"
                                                  label="{{ __('Відправити файли') }}"
                                                  svgId="svg-arrow-right"
                        />
                    </x-slot>
                </x-forms.form-group>
            @endif
        @endif

        @if($isUploaded || empty($patientRequest->uploadedDocuments))
            <h5 class="mb-8 text-2xl font-semibold text-gray-900 dark:text-white">
                {{ __('Код з СМС') }}
            </h5>
            <x-forms.form-row gap="gap-3" class="{{ empty($patientRequest->uploadedDocuments) ? 'mt-0' : 'mt-8' }} mb-14">

                <x-forms.form-group class="xl:w-1/3">
                    <x-slot name="input">
                        <x-forms.input class="default-input"
                                       wire:model="patientRequest.verificationCode"
                                       type="text"
                                       id="verification_code"
                                       maxlength="4"
                                       placeholder="{{ __('Код підтвердження з СМС') }}"
                        />
                    </x-slot>

                    @error('patientRequest.verificationCode')
                    <x-slot name="error">
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                    </x-slot>
                    @enderror
                </x-forms.form-group>

                @if(!$isApproved)
                    <div>
                        <button wire:click="approvePerson('verificationCode')"
                                type="button"
                                class="default-button"
                        >
                            {{ __('forms.confirm') }}
                        </button>
                    </div>

                    <!-- Resend SMS button -->
                    <div>
                        <button
                            type="button"
                            wire:click="resendSms"
                            x-data="{
                        cooldown: @entangle('resendCooldown'),
                        interval: null,
                        startCooldown() {
                            if (this.interval) {
                                clearInterval(this.interval);
                                this.interval = null;
                            }
                            if (this.cooldown > 0) {
                                this.interval = setInterval(() => {
                                    if (this.cooldown > 0) {
                                        this.cooldown--;
                                    } else {
                                        clearInterval(this.interval);
                                        this.interval = null;
                                    }
                                }, 1000);
                            }
                        },
                    }"
                            x-init="startCooldown()"
                            x-effect="startCooldown()"
                            x-bind:disabled="cooldown > 0"
                            x-bind:class="{ 'cursor-not-allowed': cooldown > 0 }"
                            class="light-button px-3 flex items-center gap-2"
                        >
                            <svg width="16" height="17">
                                <use xlink:href="#svg-mail"></use>
                            </svg>
                            <span
                                x-text="cooldown > 0 ? `Відправити ще раз (через ${cooldown} с)` : 'Відправити ще раз'">
                            </span>
                        </button>
                    </div>
                @else
                    <svg width="46" height="46">
                        <use xlink:href="#svg-badge-check"></use>
                    </svg>
                @endif
            </x-forms.form-row>
        @endif

        @if($isInformed)
            <div class="mt-16">
                <button wire:click="create('signedContent')"
                        type="button"
                        class="default-button flex items-center gap-2"
                >
                    <svg width="16" height="17">
                        <use xlink:href="#svg-key"></use>
                    </svg>
                    {{ __('Підписати КЕПом') }}
                    <svg width="16" height="17">
                        <use xlink:href="#svg-arrow-right"></use>
                    </svg>
                </button>
            </div>
        @endif

        @if($showModal === 'signedContent')
            @include('livewire.patient._parts.modals._modal_signed_content')
        @elseif($showModal === 'patientLeaflet')
            @include('livewire.patient._parts.modals._modal_leaflet')
        @endif
    </div>
</div>
