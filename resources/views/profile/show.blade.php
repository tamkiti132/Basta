<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div>
        <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')

            <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div class="mt-10 sm:mt-0">
                @livewire('profile.update-password-form')
            </div>

            <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <div class="mt-10 sm:mt-0">
                @livewire('profile.two-factor-authentication-form')
            </div>

            <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            <x-section-border />

            <div class="mt-10 sm:mt-0">
                <x-action-section>
                    <x-slot name="title">
                        {{ __('ソーシャルログイン設定') }}
                    </x-slot>

                    <x-slot name="description">
                        
                    </x-slot>

                    <x-slot name="content">
                        <div class="max-w-xl text-sm text-gray-600">
                            <p>別ウィンドウで表示します。</p>
                        </div>

                        <div class="mt-5">
                            <button
                                class="px-6 py-2 text-sm font-bold text-white bg-gray-800 border-0 rounded-md focus:outline-none hover:bg-gray-700"
                                onclick="location.href='{{ route('social_login_connect') }}' ">連携する</button>
                        </div>
                    </x-slot>
                </x-action-section>
            </div>

            <x-section-border />

            <div class="mt-10 sm:mt-0">
                <x-action-section>
                    <x-slot name="title">
                        {{ __('クレジットカード情報') }}
                    </x-slot>

                    <x-slot name="description">
                        {{-- --}}
                    </x-slot>

                    <x-slot name="content">
                        <div class="max-w-xl text-sm text-gray-600">
                            <p>別ウィンドウで表示します。</p>
                        </div>

                        <div class="mt-5">
                            <button
                                class="px-6 py-2 text-sm font-bold text-white bg-indigo-500 border-0 rounded-md focus:outline-none hover:bg-indigo-400"
                                onclick="location.href='{{ route('creditcard') }}' ">編集する</button>
                        </div>

                        <!-- Delete User Confirmation Modal -->

                    </x-slot>
                </x-action-section>
            </div>

            <x-section-border />

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <div class="mt-10 sm:mt-0">
                @livewire('custom-delete-user-form')
            </div>
            @endif
        </div>
    </div>
</x-app-layout>