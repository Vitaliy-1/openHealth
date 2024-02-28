@props(['submit'])
<div {{ $attributes->merge(['class' => 'grid grid-cols-1 gap-9']) }}>
    <div class="flex flex-col gap-9">

        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <form wire:submit="{{ $submit }}">
                <x-forms.form-title>
                    <x-slot name="title">{{ $title }}</x-slot>
                </x-forms.form-title>
                {{ $form }}
                @if (isset($actions))
                    <div class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-800 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        {{ $actions }}
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
</div>


