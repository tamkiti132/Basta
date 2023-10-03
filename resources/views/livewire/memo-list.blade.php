<div>
    <x-slot name="header">
        <div class="grid items-center grid-cols-2">
            {{-- 左側 --}}
            <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
                グループトップページ
            </h2>

            {{-- 右側 --}}
            <div class="flex items-center justify-end gap-5 sm:gap-20">
                <a class="text-sm font-semibold leading-tight text-gray-800 sm:text-xl"
                    href="{{ route('group.memo_create.create') }}">
                    メモ投稿
                </a>

                <!-- 三点リーダー（モーダル） -->
                <div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                <i class="px-5 text-3xl fas fa-ellipsis-v"></i>
                            </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                            <div class="flex flex-col px-4 text-gray-800">

                                @can('manager', $group_data)
                                <button
                                    onclick="location.href='{{ route('group.group_edit.edit', ['group_id' => $group_data['id']]) }}' "
                                    class="block w-full p-2 text-left hover:bg-slate-100">
                                    グループ編集
                                </button>
                                @endcan

                                <button onclick="Livewire.emit('showModalReportGroup')"
                                    class="block w-full p-2 text-left hover:bg-slate-100">
                                    グループを通報
                                </button>

                                <button onclick="Livewire.emit('showModal')"
                                    class="block w-full p-2 text-left hover:bg-slate-100">
                                    退会
                                </button>

                                <form method="POST"
                                    action="{{ route('group.destroy', ['id' => session()->get('group_id') ]) }}">
                                    @csrf
                                    <button type="submit" class="block w-full p-2 text-left hover:bg-slate-100"
                                        onclick="return confirm('本当に削除しますか？');">グループを削除</button>
                                </form>

                                <button class="block w-full p-2 text-left hover:bg-slate-100">グループを利用停止</button>

                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

            </div>
            {{-- グループ通報モーダル --}}
            @livewire('report-group')

            {{-- 退会確認モーダル --}}
            @livewire('quit-group-form')
            {{-- @livewire('counter') --}}

            {{-- 次の管理者選択モーダル --}}


            {{-- サブ管理者いませんよモーダルモーダル --}}
            <div x-cloak x-show="modal_nobody_submanager"
                class="fixed top-0 left-0 z-30 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
                <div x-on:click.away="modal_nobody_submanager = false"
                    class="flex flex-col justify-center w-full h-auto max-w-sm px-3 py-2 bg-white rounded-xl">
                    <div class="flex flex-col items-center pb-2 mb-6">
                        <div class="object-cover w-8 h-8 bg-blue-200 rounded-full"></div>
                        <p>グループ名</p>
                    </div>

                    <div class="flex justify-center mb-6 text-sm font-bold text-center">
                        <p class="leading-relaxed">本当に退会しますか？<br>
                            （グループ内で投稿したメモ、コメントは残ります）</p>
                    </div>

                    <div action="" method="GET" class="flex flex-col gap-2 p-2">

                        <label for="password">パスワード</label>
                        <input type="password" id="password" class="text-sm rounded-lg sm:text-base">

                        <div class="flex flex-col items-center justify-center gap-5 pt-2 text-sm sm:flex-row sm:gap-2">
                            <button class="w-48 px-1 py-2 text-red-500 border border-red-500 hover:bg-red-50"
                                x-on:click="">サブ管理者を設定しない</button>
                            <button class="w-48 px-1 py-2 border border-gray-300 hover:bg-slate-100"
                                x-on:click="modal_nobody_submanager = false">サブ管理者を設定する</button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </x-slot>

    {{-- <div class="pt-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <form method="get" action="{{ route('group.index', ['group_id' => $group_data['id']]) }}" class="text-left">
            <input type="text" name="search" placeholder="タイトルかメモ概要のワードで検索" class="rounded-xl" size="50">
            <button class="px-3 py-2 font-bold">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div> --}}
    {{-- <div class="pt-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <form wire:submit.prevent.defer>
            <input type="text" wire:model="search" placeholder="タイトルかメモ概要のワードで検索" class="rounded-xl" size="50">
            <button class="px-3 py-2 font-bold" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div> --}}
    <div class="pt-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <form wire:submit.prevent="executeSearch">
            <input type="text" wire:model.defer="search" placeholder="タイトルかメモ概要のワードで検索" class="rounded-xl" size="50">
            <button class="px-3 py-2 font-bold" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>
    <div class="relative py-12 xl:grid-cols-12 xl:grid">

        {{-- ラベル一覧（左） --}}
        <div class="absolute z-20 col-span-2 sm:block xl:static">
            <input type="checkbox" id="drawer-toggle" class="sr-only peer" checked>
            <label for="drawer-toggle" class="left-0 inline-block p-2 bg-indigo-500 rounded-lg xl:hidden top-40 ">
                <div class="w-6 h-1 mb-3 bg-white rounded-lg"></div>
                <div class="w-6 h-1 bg-white rounded-lg"></div>
            </label>
            <div class="hidden h-full bg-white shadow-lg rounded-r-2xl peer-checked:block">

                <div class="z-20 px-1 py-2 sm:static">
                    {{-- ラベル表示 --}}
                    <div class="xl:col-span-2">

                        @livewire('web-book-label')

                        @livewire('label-list')

                        {{-- ラベル編集 --}}
                        <div>
                            @can('manager', $group_data)
                            <div>
                                <button onclick="Livewire.emit('showLabelEditModal')"
                                    class="flex items-center w-full gap-4 p-2 hover:bg-slate-100"><i
                                        class="sm:text-2xl fa-solid fa-pencil fa-fw"></i>
                                    <p class="text-sm sm:text-base">ラベルを編集</p>
                                </button>
                            </div>
                            @endcan

                            {{-- ラベル編集モーダル --}}


                            {{-- ラベル名入力 --}}
                            @livewire('label-editor')


                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- メインコンテンツ（中央） --}}
        <div class="w-full mx-auto xl:col-span-8 max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">

                <x-flash-message status="suspension" />
                <x-flash-message status="error" />

                {{-- @php
                dump($all_memos_data_paginated);
                @endphp --}}
                @foreach ($all_memos_data_paginated as $memo_data)
                {{-- @php
                dump($memo_data->labels);
                @endphp --}}
                @if ($memo_data['type'] == 0 )
                {{-- Webタイプ　の場合 --}}
                <section class="text-gray-600 body-font">
                    <div class="container px-5 mx-auto">
                        <div class="-m-4 ">
                            <div class="p-4">
                                <div
                                    class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                                    <div class="grid gap-10 sm:grid-cols-7 sm:gap-0">
                                        {{-- 左側 --}}
                                        <div class="sm:col-span-3">
                                            <div class="flex items-center content-center">
                                                {{-- photo --}}
                                                @if($memo_data->profile_photo_path)
                                                <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                                                    onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' ">
                                                    <img class="object-fill rounded-full h-14 w-14"
                                                        src="{{ asset('storage/'. $memo_data->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"
                                                    onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "></button>
                                                @endif
                                                {{-- メモ作成者情報 --}}
                                                <div class="text-xs">
                                                    <div class="mb-1 sm:mb-0">
                                                        <p class="ml-3 text-black sm:text-base">
                                                            {{ $memo_data['nickname'] }}
                                                        </p>
                                                        <button class="ml-5 text-gray-500 sm:text-sm"
                                                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' ">
                                                            {{ $memo_data['username'] }}
                                                        </button>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>
                                                            {{ $memo_data['created_at']->format('Y-m-d') }}
                                                        </span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                                                        <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                        <span>
                                                            {{ $memo_data['updated_at']->format('Y-m-d')}}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- メモタイトル --}}
                                            <div class="mt-5 ml-3 leading-none y-4">
                                                <button
                                                    class="font-bold text-left text-gray-700 break-all sm:text-xl title-font"
                                                    onclick="location.href='{{ route('group.memo_show.show', ['id' => $memo_data['id'], 'type' => 'web']) }}' ">{{
                                                    $memo_data['title'] }}
                                                </button>
                                            </div>
                                            {{-- 『いいね』 『あとでよむ』 --}}
                                            <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                                                <div class="w-20">
                                                    {{-- <i
                                                        class="inline text-pink-300 sm:text-lg fa-solid fa-regular fa-heart"></i>
                                                    --}}
                                                    @livewire('good-button', ['memo' => $memo_data],
                                                    key('good-button-'.microtime(true)))
                                                    {{-- <span class="ml-1">3</span> --}}
                                                </div>
                                                <div class="w-20">
                                                    {{-- <i class="inline sm:text-lg fa-solid fa-file"></i> --}}
                                                    @livewire('later-read-button', ['memo' => $memo_data],
                                                    key('later-read-button-'.microtime(true)))
                                                    {{-- <span class="ml-2">2</span> --}}
                                                </div>
                                            </div>
                                            {{-- タグ --}}
                                            <div class="mt-8 text-xs sm:text-sm">
                                                @foreach ($memo_data->labels as $label)
                                                <div
                                                    class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                                                    {{ $label->name }}</div>
                                                @endforeach
                                            </div>


                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="sm:col-span-3">
                                            {{-- shortMemo --}}
                                            <div class="flex">
                                                <p class="mb-3 text-sm leading-relaxed break-all sm:text-base">
                                                    {!! nl2br(e($memo_data['shortMemo'])) !!}
                                                </p>
                                            </div>

                                            {{-- ボタン --}}
                                            <div class="grid gap-10 px-10 pt-10 sm:px-0 sm:grid-cols-2 sm:pt-40">
                                                @if ($memo_data['user_id'] === Auth::id() )
                                                <button
                                                    class="px-10 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                    onclick="location.href='{{ route('group.memo_edit.edit', ['id' => $memo_data['id'], 'type' => 'web'] ) }}' ">編集する</button>
                                                @else
                                                <div class=""></div>
                                                @endif
                                                <button
                                                    class="px-10 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                    onclick="window.open('{{ $memo_data['url'] }}') ">リンクを開く</button>
                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="hidden sm:grid-cols-5 sm:grid">
                                            <div class="sm:col-span-2">
                                            </div>
                                            <div class="sm:col-span-3">
                                                <div class="text-right">
                                                    <i class="text-3xl fas fa-globe"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </section>
                @elseif ($memo_data['type'] == 1 )
                {{-- 本タイプ　の場合 --}}
                <section class="text-gray-600 body-font">
                    <div class="container px-5 mx-auto">
                        <div class="-m-4 ">
                            <div class="p-4">
                                <div
                                    class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                                    <div class="grid gap-10 sm:grid-cols-7 sm:gap-0">
                                        {{-- 左側 --}}
                                        <div class="sm:col-span-3">
                                            <div class="flex items-center content-center">
                                                {{-- photo --}}
                                                @if($memo_data->profile_photo_path)
                                                <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                                                    onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' ">
                                                    <img class="object-fill rounded-full h-14 w-14"
                                                        src="{{ asset('storage/'. $memo_data->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"
                                                    onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "></button>
                                                @endif
                                                {{-- メモ作成者情報 --}}
                                                <div class="text-xs">
                                                    <div class="mb-1 sm:mb-0">
                                                        <p class="ml-3 text-black sm:text-base">
                                                            {{ $memo_data['nickname'] }}
                                                        </p>
                                                        <button class="ml-5 text-gray-500 sm:text-sm"
                                                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' ">
                                                            {{ $memo_data['username'] }}
                                                        </button>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>
                                                            {{ $memo_data['created_at']->format('Y-m-d') }}
                                                        </span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                                                        <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                        <span>
                                                            {{ $memo_data['updated_at']->format('Y-m-d')}}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- メモタイトル --}}
                                            <div class="mt-5 ml-3 leading-none y-4">
                                                <button
                                                    class="font-bold text-left text-gray-700 break-all sm:text-xl title-font"
                                                    onclick="location.href='{{ route('group.memo_show.show', ['id' => $memo_data->id, 'type' => 'book'] ) }}' ">{{
                                                    $memo_data['title'] }}
                                                </button>
                                            </div>
                                            {{-- 『いいね』 『あとでよむ』 --}}
                                            <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                                                <div class="w-20">
                                                    {{-- <i class="inline sm:text-lg fa-regular fa-heart"></i> --}}
                                                    @livewire('good-button', ['memo' => $memo_data],
                                                    key('good-button-'.microtime(true)))
                                                    {{-- <span class="ml-1">3</span> --}}
                                                </div>
                                                <div class="w-20">
                                                    {{-- <i class="inline sm:text-lg fa-regular fa-file"></i> --}}
                                                    @livewire('later-read-button', ['memo' => $memo_data],
                                                    key('later-read-button-'.microtime(true)))
                                                    {{-- <span class="ml-2">2</span> --}}
                                                </div>
                                            </div>
                                            {{-- タグ --}}
                                            <div class="mt-8 text-xs sm:text-sm">
                                                @foreach ($memo_data->labels as $label)
                                                <div
                                                    class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                                                    {{ $label->name }}</div>
                                                @endforeach
                                            </div>


                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="sm:col-span-3">
                                            {{-- shortMemo --}}
                                            <div class="flex">
                                                <p class="mb-3 text-sm leading-relaxed break-all sm:text-base">{!!
                                                    nl2br(e($memo_data['shortMemo'])) !!}
                                                </p>
                                            </div>

                                            {{-- ボタン --}}
                                            <div class="grid gap-10 px-10 pt-10 sm:px-0 sm:grid-cols-2 sm:pt-40">
                                                @if ($memo_data['user_id'] === Auth::id() )
                                                <button
                                                    class="px-10 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                    onclick="location.href='{{ route('group.memo_edit.edit', ['id' => $memo_data['id'], 'type' => 'book'] ) }}' ">編集する</button>
                                                @else
                                                <div class=""></div>
                                                @endif

                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="grid grid-cols-5">
                                            <div class="col-span-5">
                                                <div class="max-w-xs m-auto">
                                                    <div class="hidden text-right xl:block">
                                                        <i class="text-3xl fas fa-book-open"></i>
                                                    </div>
                                                    @if($memo_data['book_photo_path'])
                                                    <img
                                                        src="{{ asset('storage/book-image/'. basename($memo_data['book_photo_path'])) }}" />
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                </section>

                @endif
                @endforeach


            </div>
        </div>
    </div>

    <div class="flex justify-center">
        {{ $all_memos_data_paginated->links() }}
    </div>
</div>