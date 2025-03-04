@php
    $hasPublicOfferKnedpError = $errors->has('knedp');
    $hasPublicOfferPasswordError = $errors->has('password');
    $hasPublicOfferFileError = $errors->has('keyContainerUpload');
@endphp

<fieldset
    class="fieldset"
    xmlns="http://www.w3.org/1999/html"
    x-data="{ title: '{{ __('forms.complete') }}', index: 8 }"
    x-init="typeof addHeader !== 'undefined' && addHeader(title, index)"
    x-show="activeStep === index  || isEdit"
    x-cloak
    :key="`step-${index}`"
>
    <template x-if="isEdit">
        <legend x-text="title" class="legend"></legend>
    </template>

    <div class='form-row lg:w-1/2 sm:w-1/2'>
        <div class="form-group group pb-4">
            <select
                required
                id="publicOfferKnedp"
                wire:model="knedp"
                {{-- aria-describedby="{{ $hasPublicOfferKnedpError ? 'publicOfferKnedpErrorHelp' : '' }}" --}}
                class="input-select text-gray-800 {{ $hasPublicOfferKnedpError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            >
                <option value="_placeholder_" selected hidden>-- {{ __('forms.select') }} --</option>

                @foreach($getCertificateAuthority as $k => $certificate_type)
                    <option value="{{ $certificate_type['id'] }}">{{ $certificate_type['name'] }}</option>
                @endforeach
            </select>

            @if($hasPublicOfferKnedpError)
                <p id="publicOfferKnedpErrorHelp" class="text-error">
                    {{ $errors->first('knedp') }}
                </p>
            @endif

            <label for="publicOfferKnedp" class="label z-10">
                {{ __('forms.KNEDP') }}
            </label>
        </div>

        <div class="form-group group py-4">
            <x-forms.file
                required
                wire:model="keyContainerUpload"
                file="{{ $keyContainerUpload?->getClientOriginalName() }}"
                :id="'keyContainerUpload'"
            />

            @if($hasPublicOfferFileError)
                <p id="publicOfferFileErrorHelp" class="text-error">
                    {{ $errors->first('keyContainerUpload') }}
                </p>
            @endif

            <label for="keyContainerUpload" class="label z-10">
                {{ __('forms.keyContainerUpload') }} *
            </label>
        </div>

        <div class="form-group group">
            <input
                required
                type="password"
                placeholder=" "
                id="publicOfferPassword"
                wire:model="password"
                {{-- aria-describedby="{{ $hasPublicOfferPasswordError ? 'publicOfferPasswordErrorHelp' : '' }}" --}}
                class="input {{ $hasPublicOfferPasswordError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasPublicOfferPasswordError)
                <p id="publicOfferPasswordErrorHelp" class="text-error">
                    {{ $errors->first('password') }}
                </p>
            @endif

            <label for="publicOfferPassword" class="label z-10">
                {{ __('forms.password') }}
            </label>
        </div>
    </div>
</fieldset>
