<div {!! $attributes->merge(['class' => '']) !!}>
    {{$label ?? ''}}
    @isset($input)
    {{$input}}
    @endisset
    @isset($error)
    {{$error}}
    @endisset
</div>
