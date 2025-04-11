<!-- ===== Messsages Start ===== -->
<div
    class="alert-message flex fixed top-[1.5rem] w-auto z-[100000] right-2"
    x-init="setTimeout(() => document.querySelector('.alert-message').remove(), 3000)"
>
    @if(session('error'))
        <div role="alert"
                class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
        >
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div role="alert"
                class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
        >
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('status'))
        <x-message.successes>
            <x-slot name="status">{{ session('status') }}</x-slot>
        </x-message.successes>
    @endif
</div>
<!-- ===== Messsages End ===== -->
