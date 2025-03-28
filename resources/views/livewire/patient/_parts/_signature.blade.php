<fieldset class="fieldset">
    @if(!empty($uploadedDocuments))
        <legend class="legend">
            {{ __('Завантаження документів') }}
        </legend>

        @foreach($uploadedDocuments as $key => $document)
            <div class="pb-4 flex" wire:key="{{ $key }}">
                <div class="flex-grow">
                    <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white"
                           for="file_input_{{ $key }}"
                    >
                        {{ __('patients.documents.' . Str::lower(Str::afterLast($document['type'], '.'))) }}
                    </label>
                    <div class="flex items-center gap-4">
                        <input
                            class="xl:w-1/2 block text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            id="file_input_{{ $key }}"
                            type="file"
                            wire:model.live="form.uploadedDocuments.{{ $key }}"
                        >

                        @if(isset($patientRequest->uploadedDocuments[$key]) && !$errors->has("form.uploadedDocuments.$key"))
                            @if(!isset($uploadedFiles[$key]) || $uploadedFiles[$key] === false)
                                <button class="flex items-center gap-1"
                                        wire:click.prevent="deleteDocument({{ $key }})"
                                >
                                    <svg width="14" height="14" class="text-red-600">
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

                    @error("form.uploadedDocuments.$key")
                    <p class="text-error">
                        {{ $message }}
                    </p>
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

    @if($isUploaded || empty($uploadedDocuments))
        <h2 class="mb-8 text-2xl font-semibold text-gray-900 dark:text-white">
            {{ __('Код з СМС') }}
        </h2>

        <div class="flex flex-col md:flex-row gap-4 md:gap-6 {{ empty($uploadedDocuments) ? 'mt-0' : 'mt-8' }} mb-14">
            <div class="relative z-0 md:min-w-[33%] md:max-w-[33%]">
                <input wire:model="form.verificationCode"
                       type="text"
                       name="verificationCode"
                       id="verificationCode"
                       class="input peer @error('form.verificationCode') input-error @enderror"
                       placeholder=" "
                       required
                       maxlength="4"
                       autocomplete="off"
                />
                <label for="verificationCode" class="label">
                    {{ __('Код підтвердження з СМС') }}
                </label>

                @error('form.verificationCode')
                <p class="text-error">
                    {{ $message }}
                </p>
                @enderror
            </div>

            @if(!$isApproved)
                <div>
                    <button wire:click="approvePerson('verificationCode')"
                            type="button"
                            class="default-button w-full"
                    >
                        {{ __('forms.confirm') }}
                    </button>
                </div>

                <!-- Resend SMS button -->
                <div>
                    <button
                        type="button"
                        wire:click.prevent="resendSms"
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
                        :disabled="cooldown > 0"
                        :class="{ 'cursor-not-allowed': cooldown > 0 }"
                        class="light-button px-3 flex items-center gap-2 w-full"
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
        </div>
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
                {{ __('forms.sign_with_KEP') }}
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
</fieldset>
