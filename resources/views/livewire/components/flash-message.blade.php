<div>
    @if($message || $errors)
        <div
            class="alert-message flex fixed top-[1.5rem] w-auto z-[100000] right-2"
            x-init="setTimeout(() => document.querySelector('.alert-message').remove(), 3000)"
        >
            @if(!$errors)
                @if($type === 'error')
                    <div role="alert"
                         class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                    >
                        <span class="font-medium">{{ $message }}</span>
                    </div>
                @endif
                @if($type === 'success')
                    <div role="alert"
                         class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                    >
                        <span class="font-medium">{{ $message }}</span> .
                    </div>
                @endif
            @endif
            @if(!empty($errors))
                <div role="alert"
                     class="flex p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                >
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <div>
                        <ul class="mt-1.5 list-disc list-inside space-y-1">
                            @foreach($errors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
