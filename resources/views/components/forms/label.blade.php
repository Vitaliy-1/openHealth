@props([
    'value',
    'isRequired'=>false,
    'disabled' => false
])

<label {{ $attributes->merge([
                'class' => 'block mb-2 text-sm font-medium dark:text-white',
                'x-bind:class' => "{
                    'text-gray-400': typeof disabled !== 'undefined' && disabled,
                    'text-gray-900': typeof disabled === 'undefined' || !disabled
                }"
            ])
        }}
>
    {{ $value ?? $slot }} {{$isRequired ? '*' : ''}}
</label>
