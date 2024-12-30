
<x-empty>
    @nonempty($pagination)
    @if($pagination->total() > $pagination->perPage())
    <div class="{{ $class }}" style="{{ $style ?? ''}}">
        {{ $pagination->links() }}
    </div>
    @endif
    @endnonempty
</x-empty>
