@props(['lists'])

<div {{$attributes->merge(['class' => 'z-10  bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700'])}}  >
    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" >
    {{$lists}}
    </ul>
</div>
