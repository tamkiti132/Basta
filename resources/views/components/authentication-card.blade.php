<div class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0">
    <x-flash-message status="error" />
    <div>
        {{ $logo }}
    </div>

    <div class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg">
        {{ $slot }}
    </div>

    {{-- 新規登録画面でGoogleログインを表示されないようにするために記載 --}}
    @if(Route::is('login'))
    <a href="{{ route('auth.google') }}"
        class="flex items-center justify-center px-32 py-3 mt-8 text-lg font-bold text-gray-700 bg-white border border-gray-700 rounded-md hover:bg-gray-100">
        <img src="/images/google.png" alt="Google" class="w-6 h-6 mr-2">Googleでログイン
    </a>
    @endif
</div>
