@php
    $svgSprite = file_get_contents(resource_path('images/sprite.svg'));
@endphp

<x-layouts.patient :id="$id" :firstName="$firstName" :lastName="$lastName" :secondName="$secondName">
    <div aria-hidden="true" class="hidden">
        {!! $svgSprite !!}
    </div>

    <div class="breadcrumb-form p-4">

    </div>

    <x-forms.loading/>
</x-layouts.patient>
