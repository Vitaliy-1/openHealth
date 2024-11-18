@props(['cols' => 'xl:flex-row', 'gap' => 'gap-6'])
<div {!! $attributes->merge(['class' => 'mb-4 flex ' . $cols . ' ' . $gap]) !!}>
    {{ $slot }}
</div>
