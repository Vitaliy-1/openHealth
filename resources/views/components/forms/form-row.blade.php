@props(['cols' => 'xl:flex-row', 'gap' => 'gap-6', 'mb' => '4'])

<div {!! $attributes->merge(['class' => 'mb-' . $mb . ' flex ' . $cols . ' ' . $gap]) !!}>
    {{ $slot }}
</div>
