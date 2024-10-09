@props(['navigation','title','description'])

<div {{$attributes->merge(['class' => 'p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700'])}}>
   <div class="w-full mb-1">
    <x-section-title>
        <x-slot name="title"> {{$title}}</x-slot>
        <x-slot name="description">{{$title}}</x-slot>
    </x-section-title>
        {{$navigation ?? ''}}
   </div>
</div>
