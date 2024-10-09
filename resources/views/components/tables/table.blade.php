@props(['headers'])

<table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600']) }}>
    <thead class="bg-gray-100 dark:bg-gray-700">
    <tr >
        @foreach ($headers->attributes['list'] as $key => $header)
            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">{{ $header }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
    {{ $tbody }}
    </tbody>

</table>

