@props(['submit'])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="flex flex-col gap-9">

        <div class=" bg-white ">
            <form  wire:submit.prevent="{{ $submit }}">
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
