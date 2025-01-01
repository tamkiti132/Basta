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

<body class="font-sans antialiased" x-data="{modal_report_user: false,
                                            modal_report_memo: false,
                                            modal_report_comment: false,
                                            modal_label_edit: false,
                                            modal_select_next_manager: false,
                                            modal_nobody_submanager: false,
                                            modal_confirm_delete_group: false,
                                            member: true,
                                            block_member: false,
                                            user: true,
                                            suspension_user: false}">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        <!-- ナビゲーションメニュー -->
        @livewire('navigation-menu')

        <!-- ヘッダー -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="px-4 py-4 mx-auto text-xs sm:px-6 lg:px-8 sm:text-sm">
                {{ $header }}
            </div>
        </header>
        @endif

        <x-flash-message status="success" />
        <x-flash-message status="error" />
        <x-flash-message status="role-access-error" />
        <x-flash-message status="blockedUser" />
        <x-flash-message status="suspension" />
        <x-flash-message status="not_member" />
        <x-flash-message status="isNotJoinFreeEnabled" />


        <!-- メインコンテンツ -->
        <main class="overflow-hidden">
            {{ $slot }}
        </main>

        <!-- フッター -->
        <footer class="px-4 py-10 mx-auto mt-10 bg-white shadow sm:mt-20 sm:px-6 lg:px-8">
            {{-- 運営トップ --}}
            <div class="flex flex-wrap justify-between text-xs font-bold sm:flex-nowrap sm:text-sm">
                {{-- 左側 --}}
                <div class="w-auto">
                    @can('admin-top')
                    <a href="{{ route('admin.admin_user_top') }}">運営ユーザー一覧</a>
                    @endcan
                </div>
                {{-- 右側 --}}
                <div class="w-auto text-right">
                    @can('admin-top')
                    <div>
                        <a href="{{ route('register') }}">運営権限ユーザー新規登録</a>
                    </div>
                    @endcan
                    <div class="mt-2">
                        <a class="font-bold" href="{{ route('request') }}">リクエストを送信</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @stack('modals')


    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>


</body>

</html>