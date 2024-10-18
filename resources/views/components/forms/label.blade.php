@props(['value', 'isRequired'=>false])

<label {{ $attributes->merge(['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) }}>
    {{ $value ?? $slot }} {{$isRequired ? '*' : ''}}
</label>
