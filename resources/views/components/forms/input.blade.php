@props(['disabled' => false, 'autocomplete' => 'off-all'])

<input autocomplete="{{ $autocomplete }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'default-input']) !!}>
