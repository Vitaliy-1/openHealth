<div {!! $attributes->merge(['class' => 'w-full']) !!}>
    {{$label}}
    @isset($input)

    {{$input}}
    @endisset
    @isset($error)
    {{$error}}
    @endisset
</div>
