<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {{--
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    <script src="https://kit.fontawesome.com/8e73e32fc5.js" crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />


    @livewireScripts
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<!-- TODO:各ブレードファイルに分割する -->

<body class="font-sans antialiased" x-data="{modal_report_group: false,
                                            modal_report_user: false,
                                            modal_report_memo: false,
                                            modal_report_comment: false,
                                            modal_label_edit: false,
                                            modal_select_next_manager: false,
                                            modal_nobody_submanager: false,
                                            modal_confirm_delete_group: false,
                                            modal_label_select: false,
                                            form_web: true,
                                            form_book: false,
                                            member: true,
                                            block_member: false,
                                            user: true,
                                            suspension_user: false,
                                            defect: false,
                             function_addition_improvement: false,
                             vulnerability: false,
                             other: false }">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        @if (isset($header))
        <header class="bg-white shadow">
            <div class="px-4 py-6 mx-auto sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- メインコンテンツ -->
        <main class="overflow-hidden">
            {{ $slot }}
        </main>

        <!-- フッター -->
        <footer class="mt-10 bg-white shadow sm:mt-20">
            {{-- （運営トップ or　運営） 以外のユーザー --}}
            <div class="px-4 py-10 mx-auto text-right sm:px-6 lg:px-8">
                <a class="font-bold sm:text-lg" href="{{ route('request') }}">リクエストを送信</a>
            </div>

            {{-- 運営トップ or　運営 --}}
            {{-- <div
                class="flex flex-wrap gap-3 px-4 py-10 mx-auto font-bold sm:flex-nowrap sm:gap-0 sm:justify-between sm:text-lg sm:px-6 lg:px-8">
                <div class="w-full sm:w-auto">
                    <a href="{{ route('admin/admin/user/show') }}">運営ユーザー一覧</a>
                </div>
                <div class="w-full sm:w-auto">
                    <a href="">運営権限ユーザー新規登録</a>
                </div>
            </div> --}}
        </footer>
    </div>

    @stack('modals')


    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>


</body>

</html>