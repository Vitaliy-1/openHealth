@props(['status'])




<span class="inline-flex items-center text-center {{$status->value}}   text-xs font-medium px-2.5 py-0.5 rounded-full">
               {{$status->label()}}
</span>
