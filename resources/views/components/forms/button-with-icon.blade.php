@props([
    'label' => '',
    'svgId' => null,
    'width' => 16,
    'height' => 16
])

<button {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    @if ($svgId)
        <svg width="{{ $width }}" height="{{ $height }}">
            <use xlink:href="#{{ $svgId }}"></use>
        </svg>
    @endif
    <span>{{ $label }}</span>
</button>
