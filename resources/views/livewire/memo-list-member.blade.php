<div>
    <x-slot name="header">
        <div class="grid grid-cols-2">
            {{-- 左側 --}}
            <div class="flex">
                {{-- <div class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-10 sm:h-10"></div> --}}
                @if($user_data->profile_photo_path)
                <div class="object-cover w-8 h-8 mr-3 bg-center rounded-full sm:w-10 sm:h-10">
                    <img class="object-fill w-8 h-8 rounded-full sm:w-10 sm:h-10"
                        src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                </div>
                @else
                <div class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"></div>
                @endif
                <div>
                    <h2 class="text-sm font-semibold leading-tight text-gray-800 sm:text-xl">
                        {{ $user_data->nickname }} <span class="hidden">のページ</span>
                    </h2>
                    <div class="flex flex-col">
                        <p class="ml-5 text-sm text-gray-500">
                            {{ $user_data->username }}
                        </p>
                        <p class="mt-3 text-sm sm:mt-0 sm:ml-28">{{ $count_all_memos_data }}<span class="ml-3">投稿</span>
                        </p>
                    </div>
                </div>
            </div>
            {{-- 右側 --}}
            <div class="flex items-center justify-end gap-5 sm:gap-20">
                <div>
                    <x-dropdown align="right" width="100">
                        <x-slot name="trigger">
                            <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                <p class="font-semibold leading-tight text-gray-800 sm:text-xl">投げ銭する</p>
                            </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                            <div class="flex flex-col pt-4 text-center text-gray-800 px-7 w-72">
                                <p>100円の投げ銭をしますか？</p>
                                <div class="flex justify-center gap-4 my-3">
                                    <button
                                        class="block px-1 py-2 text-center border border-gray-300 w-28 hover:bg-slate-100">キャンセル</button>
                                    <button
                                        class="block px-1 py-2 text-center text-blue-500 border border-blue-300 w-28 hover:bg-slate-100">はい</button>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
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
                                <button x-on:click="modal_report_user = true"
                                    class="block w-full p-2 text-left hover:bg-slate-100">
                                    ユーザーを通報
                                </button>
                                <form method="POST"
                                    action="{{ route('group.member.destroy', ['id' => $user_data['id'] ]) }}">
                                    @csrf
                                    <button type="submit" class="block w-full p-2 text-left hover:bg-slate-100"
                                        onclick="return confirm('本当に削除しますか？');">ユーザーを削除</button>
                                </form>
                                <form method="POST"
                                    action="{{ route('group.member.suspend', ['id' => $user_data['id'] ]) }}">
                                    @csrf
                                    <button type="submit" class="block w-full p-2 text-left hover:bg-slate-100"
                                        onclick="return confirm('本当に利用停止にしますか？');">ユーザーを利用停止</button>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- ユーザー通報モーダル --}}
                <div x-cloak x-show="modal_report_user"
                    class="fixed top-0 left-0 z-30 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
                    <div x-on:click.away="modal_report_user = false"
                        class="flex flex-col w-full h-auto max-w-md px-3 py-2 bg-white rounded-xl">
                        <div class="pb-2 mb-4 font-bold text-center border-b border-black">
                            <p>問題点を教えてください</p>
                        </div>

                        <form action="{{ route('group.member.storeUserTypeReport') }}" method="POST"
                            class="flex flex-col gap-2 p-2">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user_data->id }}">
                            <div>
                                <input type="radio" name="reason" value="0" class="mr-3"><span>法律違反</span>
                            </div>
                            <div>
                                <input type="radio" name="reason" value="1" class="mr-3"><span>不適切なコンテンツ</span>
                            </div>
                            <div>
                                <input type="radio" name="reason" value="2" class="mr-3"><span>フィッシング or スパム</span>
                            </div>
                            <div>
                                <input type="radio" name="reason" value="3" class="mr-3"><span>その他</span>
                            </div>

                            <label for="detail">問題がある点の詳細</label>
                            <textarea name="detail" id="detail" cols="30" rows="5" class="rounded-xl"></textarea>

                            <div class="flex justify-end gap-4 pt-2">
                                <button class="px-1 py-2 border border-gray-300 w-28 hover:bg-slate-100" type="button"
                                    x-on:click="modal_report_user=false">キャンセル</button>
                                <button type="submit"
                                    class="px-1 py-2 text-red-500 border border-red-500 w-28 hover:bg-red-50">通報</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </x-slot>

    <div class="pt-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <form wire:submit.prevent="executeSearch">
            <input type="text" wire:model.defer="search" placeholder="タイトルかメモ概要のワードで検索" class="rounded-xl" size="50">
            <button class="px-3 py-2 font-bold" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    <div class="py-12 2xl:grid-cols-12 2xl:grid">
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

                <x-flash-message status="quit" />

                @foreach ($all_memos_data_paginated as $memo_data)
                {{-- @php
                dd($memo_data);
                @endphp --}}
                @if ($memo_data->type == 0)
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
                                                {{-- メモ作成者情報 --}}
                                                <div class="text-xs">
                                                    <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>{{ $memo_data['created_at']->format('Y-m-d') }}</span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                                                        <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                        <span>{{ $memo_data['updated_at']->format('Y-m-d')}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- メモタイトル --}}
                                            <div class="mt-5 ml-3 leading-none y-4">
                                                <button
                                                    class="font-bold text-left text-gray-700 break-all sm:text-xl title-font"
                                                    onclick="location.href='{{ route('group.memo_show.show',['id' => $memo_data['id']]) }}' ">{{
                                                    $memo_data->title }}
                                                </button>
                                            </div>
                                            {{-- 『いいね』 『あとでよむ』 --}}
                                            <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                                                <div class="w-20">
                                                    @livewire('good-button', ['memo' => $memo_data],
                                                    key('good-button-'.microtime(true)))
                                                </div>
                                                <div class="w-20">
                                                    @livewire('later-read-button', ['memo' => $memo_data],
                                                    key('later-read-button-'.microtime(true)))
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
                                            <div class="mt-6 text-center sm:mt-40 sm:ml-8 sm:text-right">
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
                @elseif ($memo_data->type == 1)
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
                                                {{-- メモ作成者情報 --}}
                                                <div class="text-xs">
                                                    <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>{{ $memo_data['created_at']->format('Y-m-d') }}</span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                                                        <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                        <span>{{ $memo_data['updated_at']->format('Y-m-d')}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- メモタイトル --}}
                                            <div class="mt-5 ml-3 leading-none y-4">
                                                <button
                                                    class="self-center font-bold text-gray-700 break-all sm:text-xl title-font"
                                                    onclick="location.href='{{ route('group.memo_show.show',['id'=>$memo_data['id']]) }}' ">{{
                                                    $memo_data->title }}
                                                </button>
                                            </div>
                                            {{-- 『いいね』 『あとでよむ』 --}}
                                            <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                                                <div class="w-20">
                                                    @livewire('good-button', ['memo' => $memo_data],
                                                    key('good-button-'.microtime(true)))
                                                </div>
                                                <div class="w-20">
                                                    @livewire('later-read-button', ['memo' => $memo_data],
                                                    key('later-read-button-'.microtime(true)))
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
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="grid grid-cols-5">
                                            {{-- <div class="col-span-2">
                                            </div> --}}
                                            <div class="col-span-5">
                                                <div class="max-w-xs m-auto">
                                                    <div class="hidden text-right xl:block">
                                                        <i class="text-3xl fas fa-book-open"></i>
                                                    </div>
                                                    {{-- <img src="/images/本の画像（青）.png"> --}}
                                                    @if($memo_data['book_photo_path'])
                                                    <<img
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