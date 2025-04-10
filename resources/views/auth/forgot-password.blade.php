<x-guest-layout>
    <x-messages />

    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo/>
        </x-slot>

        <x-auth.title>
            <x-slot name="title">{{ __('Відновлення паролю') }}</x-slot>
            <x-slot name="description">{{ __('Відновлення паролю в Open Health') }}</x-slot>
        </x-auth.title>
        <div class="text-base font-normal text-gray-500 dark:text-gray-400">
            {{ __('Забули свій пароль? Без проблем. Просто повідомте нам свою електронну адресу, і ми надішлемо вам електронною поштою посилання для скидання пароля, за яким ви зможете вибрати новий.') }}
        </div>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mt-4">
                <x-label class="default-label" for="email" value="{{ __('Email') }}"/>
                <div class="relative @error('email') input-danger @enderror">
                    <x-input id="email" class="default-input" type="email" name="email" :value="old('email')"
                             autofocus autocomplete="username"/>
                    @error('email') <span
                        class="text-red-600 flex items-center font-medium tracking-wide text-danger   text-xs mt-1 ml-1">{{ $message }}</span>@enderror

                </div>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-button class="default-button w-full">
                    {{ __('Відновити пароль') }}
                </x-button>
            </div>
            <div class="mt-2 text-center">
                <p class="font-medium">
                    <a class="text-primary text-sm relative" href="{{ route('login') }}">
                        <span class="inline-block">
                       <svg class="w-2 inline-block mr-1.5 h-6 text-primary dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 1 1.3 6.326a.91.91 0 0 0 0 1.348L7 13"></path>
                        </svg>
                        {{ __('Назад') }}
                        </span>
                    </a>
                </p>
            </div>

        </form>
    </x-authentication-card>
</x-guest-layout>
