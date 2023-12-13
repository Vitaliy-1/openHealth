<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-auth.title>
            <x-slot name="title">{{ __('Відновлення паролю') }}</x-slot>
            <x-slot name="description">{{ __('Відновлення паролю в Open Health') }}</x-slot>
        </x-auth.title>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Забули свій пароль? Без проблем. Просто повідомте нам свою електронну адресу, і ми надішлемо вам електронною поштою посилання для скидання пароля, за яким ви зможете вибрати новий.') }}
        </div>
        @if (session('status'))
            <x-message.successes>
                <x-slot name="status">{{ session('status') }}</x-slot>
            </x-message.successes>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mt-4">
                <x-label class="default-label" for="email" value="{{ __('Email') }}"/>
                <div class="relative @error('email') input-danger @enderror">
                    <x-input id="email" class="default-input" type="email" name="email" :value="old('email')"
                             autofocus autocomplete="username"/>
                    <span class="absolute right-4 top-4">
                          <svg class="fill-current" width="22" height="22" viewBox="0 0 22 22" fill="none"
                               xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.5">
                              <path
                                  d="M19.2516 3.30005H2.75156C1.58281 3.30005 0.585938 4.26255 0.585938 5.46567V16.6032C0.585938 17.7719 1.54844 18.7688 2.75156 18.7688H19.2516C20.4203 18.7688 21.4172 17.8063 21.4172 16.6032V5.4313C21.4172 4.26255 20.4203 3.30005 19.2516 3.30005ZM19.2516 4.84692C19.2859 4.84692 19.3203 4.84692 19.3547 4.84692L11.0016 10.2094L2.64844 4.84692C2.68281 4.84692 2.71719 4.84692 2.75156 4.84692H19.2516ZM19.2516 17.1532H2.75156C2.40781 17.1532 2.13281 16.8782 2.13281 16.5344V6.35942L10.1766 11.5157C10.4172 11.6875 10.6922 11.7563 10.9672 11.7563C11.2422 11.7563 11.5172 11.6875 11.7578 11.5157L19.8016 6.35942V16.5688C19.8703 16.9125 19.5953 17.1532 19.2516 17.1532Z"
                                  fill="" />
                            </g>
                          </svg>
                        </span>
                    @error('email') <span class="flex items-center font-medium tracking-wide text-danger   text-xs mt-1 ml-1">{{ $message }}</span>@enderror

                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="btn-primary">
                    {{ __('Відновити пароль') }}
                </x-button>
            </div>
            <div class="mt-6 text-center">
                <p class="font-medium">

                    <a class="text-primary relative" href="{{ route('login') }}">
                        <span class="inline-block">
                       <svg class="w-2 inline-block mr-1.5 h-6 text-primary dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 1 1.3 6.326a.91.91 0 0 0 0 1.348L7 13"></path>
                        </svg>
                        {{ __('Назад') }}
                        </span>

                    </a>
                </p>
            </div>

        </form>
    </x-authentication-card>
</x-guest-layout>
