@props([
    'disabled' => false,
    'autocomplete' => 'off-all',
    'svgId' => null,
    'width' => 12,
    'height' => 12
])

<div x-data="{ value: '' }" class="relative">
    <input
        autocomplete="{{ $autocomplete }}"
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'default-input pr-10', 'x-model' => 'value']) !!}
    />

    @if($svgId)
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" @click="value = ''">
            <svg width="{{ $width }}" height="{{ $height }}">
                <use xlink:href="#{{ $svgId }}"></use>
            </svg>
        </div>
    @endif
</div>
