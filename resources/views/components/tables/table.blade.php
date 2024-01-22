@props(['headers'])

<table {{ $attributes->merge(['class' => 'w-full table-auto']) }}>
    <thead>
    <tr>
        @foreach ($headers as $key => $header)
            <th class="bg-gray-2 text-left dark:bg-meta-4">{{ $header }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
        {{ $tbody }}
    </tbody>

</table>
