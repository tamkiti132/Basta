<div class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg">
        {{ $slot }}
    </div>
    {{-- <button
        class="px-32 py-3 mt-8 text-lg font-bold bg-white border-2 border-gray-700 rounded-2xl focus:outline-none hover:bg-gray-100"><img
            src="/images/google.png">Googleでログイン</button> --}}
    <button></button>

</div>