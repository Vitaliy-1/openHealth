<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>
        <x-auth.title>
            <x-slot name="title">{{ __('Реестрація') }}</x-slot>
        </x-auth.title>
        <!-- ====== Forms Section Start -->
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div >
                <div>
                    <x-label class="default-label"  for="email" value="{{ __('Email') }}" />
                    <div class="relative @error('password') input-danger @enderror">
                        <x-input id="email" placeohlder="Email" class="default-input" type="email" name="email" :value="old('email')"  autocomplete="username" />
                        @error('email') <span class="flex items-center font-medium tracking-wide  text-red-600 text-danger   text-xs mt-1 ml-1">{{ $message }}</span>@enderror
                </div>
                </div>
            </div>

            <div class="mt-4">
                <x-label class="default-label" for="password" value="{{ __('Пароль') }}" />
                <div class="relative @error('password') input-danger @enderror">
                    <x-input id="password" class="default-input " type="password" name="password"  autocomplete="new-password" />
                </div>
                @error('password') <span class="flex items-center font-medium tracking-wide text-danger text-red-600 text-xs mt-1 ml-1">{{ $message }}</span>@enderror
            </div>

            <div class="mt-4">
                <x-label class="default-label" for="password_confirmation" value="{{ __('Підтвердьте пароль') }}" />
                <div class="relative @error('password') input-danger @enderror">
                    <x-input id="password_confirmation" class="default-input" type="password" name="password_confirmation"  autocomplete="new-password" />
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">

                <x-button class="default-button w-full">
                    {{ __('Реєстрація') }}
                </x-button>
            </div>
            <div class="mt-1 text-left">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('Вже є аккаунт?') }}
                    <a class="text-blue-600 hover:underline dark:text-blue-600" href="{{ route('login') }}">
                        {{ __('Увійти') }}
                    </a>
                </p>
            </div>

        </form>
        <!-- ====== Forms Section End -->
    </x-authentication-card>
</x-guest-layout>
