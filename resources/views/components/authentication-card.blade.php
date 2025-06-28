<div class="flex flex-col items-center pt-6 min-h-screen bg-gray-100 sm:justify-center sm:pt-0">
    <x-flash-message status="error" />
    <div>
        {{ $logo }}
    </div>

    <div class="overflow-hidden px-6 py-4 mt-6 w-full bg-white shadow-md sm:max-w-md sm:rounded-lg">
        {{ $slot }}
    </div>

    {{-- 新規登録画面でGoogleログインを表示されないようにするために記載 --}}
    @if(Route::is('login'))
    <a href="{{ route('auth.google') }}"
        class="flex justify-center items-center px-16 py-3 mt-8 text-base font-bold text-gray-700 bg-white rounded-md border border-gray-700 sm:text-lg sm:px-32 hover:bg-gray-100">
        <img src="/images/google.png" alt="Google" class="mr-2 w-5 h-5 sm:w-6 sm:h-6">Googleでログイン
    </a>
    @endif
</div>
