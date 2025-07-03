<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            {{ __('ソーシャルログイン設定') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-xl sm:px-6 lg:px-8">
        <div class="px-6 py-6 mt-10 bg-white rounded-md shadow sm:mt-20">
            <div class="flex justify-between items-center">
                <div class="flex flex-col items-start mr-4">
                    <div class="flex items-center">
                        <img src="/images/google.png" alt="Google" class="mr-2 w-6 h-6 pointer-events-none">
                        <p class="text-lg font-bold">Google</p>
                    </div>
                    @if (Auth::user()->google_id)
                        <p class="mt-2 text-sm font-bold text-cyan-500">連携済み</p>
                    @else
                        <p class="mt-2 text-sm text-gray-500">未連携</p>
                    @endif
                </div>
                <div>
                    @if (Auth::user()->google_id)
                        <button
                            onclick="if(confirm('本当に解除しますか？')) { window.location.href='{{ route('social_login_connect.google.disconnect') }}'; }"
                            class="text-sm underline"
                        >解除する</button>
                    @else
                        <button
                            onclick="if(confirm('本当に連携しますか？')) { window.location.href='{{ route('social_login_connect.google') }}'; }"
                            class="px-6 py-2 text-sm font-bold text-white bg-black rounded-md border-0 focus:outline-none hover:bg-gray-800"
                        >連携する</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ブロフィール画面へのリンク --}}
    <a href="{{ route('profile.show') }}" class="block mt-2 text-sm text-center underline">← ブロフィール画面へ</a>
</x-app-layout>
