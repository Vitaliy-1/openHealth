@props(['cols' => 'xl:flex-row'])

<div {!! $attributes->merge(['class' => 'mb-4 flex flex-col gap-6 '.$cols.' ']) !!}>
    {{ $slot }}
</div>
