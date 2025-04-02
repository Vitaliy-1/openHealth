<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>
        <x-auth.title>
            <x-slot name="title">{{ __('Вхід') }}</x-slot>
        </x-auth.title>
        @if (session('status'))
            <x-message.successes>
                <x-slot name="status">{{ session('status') }}</x-slot>
            </x-message.successes>
        @endif
        <form
            x-data="{ is_ehealth_auth: true }"
            autocomplete="off"
            method="POST"
            action="{{ route('login') }}"
        >
            @csrf
            <div>
                <x-label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="email" value="{{ __('Email') }}"/>
                <div class="relative @error('email') input-danger @enderror">
                    <x-input id="email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" type="email" name="email" :value="old('email')"
                              autofocus autocomplete="username"/>
                    @error('email') <span class="text-red-600 flex items-center font-medium tracking-wide text-danger   text-xs mt-1 ml-1">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mt-4" x-show="!is_ehealth_auth" x-cloak>
                <x-label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="password" value="{{ __('Пароль') }}" />
                <div class="relative @error('password') input-danger @enderror" >
                    <x-input id="password" class="default-input" type="password" name="password"  autocomplete="current-password" />
                    @error('password') <span class="text-red-600 flex items-center font-medium tracking-wide text-danger   text-xs mt-1 ml-1">{{ $message }}</span>@enderror

                </div>
            </div>

            <div class="block mt-4">
                <div>
                    <label for="is_local_auth" class="flex cursor-pointer select-none items-center">
                        <div class="relative">
                            <input type="checkbox" name="is_local_auth" id="is_local_auth" class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                @change="is_ehealth_auth = !$event.target.checked"
                                :checked="!is_ehealth_auth"
                            />
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Без авторизації у eHealth') }}</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="default-button w-full">
                    {{ __('Вхід') }}
                </x-button>
            </div>
            <div class="mt-6 text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    <a class="text-sm font-medium text-gray-500 dark:text-gray-400" href="{{ route('register') }}">
                        {{ __('Зареєструватися?') }} /
                    </a>
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-gray-500 dark:text-gray-400" href="{{ route('password.request') }}">
                            {{ __('Забули свій пароль?') }}
                        </a>
                    @endif
                </p>
            </div>

        </form>
    </x-authentication-card>
</x-guest-layout>
