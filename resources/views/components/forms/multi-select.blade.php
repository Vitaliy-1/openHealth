@props(['disabled' => false])




<div wire:ignore >

        <select   multiple {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => '']) !!}>
            {{$option}}
        </select>

    </div>
    @push('scripts')




@endpush
