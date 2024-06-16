<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('Contact Form') }}</title>
</head>
<body>
    <p>{{ trans('Ви отримали повідомлення від') }} {{ $name }}.</p>
    <p>{{ trans('Телефон') }}: {{ $phone }}</p>
</body>
</html>
