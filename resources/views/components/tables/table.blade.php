@props(['headers'])

<table {{ $attributes->merge(['class' => 'w-full table-auto']) }}>
    <thead>
    <tr class="bg-gray-2 text-left dark:bg-meta-4">
        @foreach ($headers->attributes['list'] as $key => $header)
            <th class="py-4 px-4 font-medium text-black dark:text-white">{{ $header }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
        {{ $tbody }}
    </tbody>

</table>
