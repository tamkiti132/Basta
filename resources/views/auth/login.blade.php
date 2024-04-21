<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="w-40">
                <x-authentication-card-logo />
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
        @endif


        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required
                    autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block w-full mt-1" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <div>
                    <a class="block mb-3 text-sm text-center text-gray-600 rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('register') }}">新規登録はこちら
                    </a>

                    @if (Route::has('password.request'))
                    <a class="text-sm text-gray-600 rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">パスワードを忘れた方はこちら
                    </a>
                    @endif
                </div>


                <x-button class="ml-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>