<div>
    <x-slot name="header">
        <div class="grid items-center grid-cols-2">
            {{-- 左側 --}}
            <h2 class="font-semibold leading-tight text-gray-800">
                グループトップページ
            </h2>

            {{-- 右側 --}}
            <div class="flex items-center justify-end gap-5 sm:gap-20">
                <a class="font-semibold leading-tight text-gray-800" href="{{ route('group.memo_create.create') }}">
                    メモ投稿
                </a>

                <!-- 三点リーダー（モーダル） -->
                <div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex transition border-2 border-transparent focus:outline-none">
                                <i class="px-5 text-lg fas fa-ellipsis-v"></i>
                            </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                            <div class="flex flex-col text-gray-800" x-data="{ isSuspended: @entangle('isSuspended') }">

                                @can('manager', $group_data)
                                <button
                                    onclick="location.href='{{ route('group.group_edit', ['group_id' => $group_data['id']]) }}' "
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

                                @can('admin-higher')
                                <button type="button" class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に削除しますか？')) { @this.call('deleteGroup', {{ $group_data->id }}) }">グループを削除</button>
                                @endcan

                                <button x-show="!isSuspended" type="button"
                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に利用停止にしますか？')) { @this.call('suspendGroup') }">
                                    グループを利用停止
                                </button>

                                <button x-show="isSuspended" type="button"
                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に利用停止解除しますか？')) { @this.call('liftSuspendGroup') }">
                                    グループを利用停止解除
                                </button>

                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

            </div>
            {{-- グループ通報モーダル --}}
            @livewire('report-group')

            {{-- 退会確認モーダル --}}
            @livewire('quit-group-form')

        </div>
    </x-slot>


    <div class="lg:grid lg:grid-cols-12">
        <div class="w-full px-6 pt-12 mx-auto lg:col-start-3 lg:col-end-10 lg:px-8 max-w-7xl">
            <form wire:submit.prevent="executeSearch">
                <input type="text" wire:model.defer="search" placeholder="タイトルかメモ概要のワードで検索"
                    class="w-64 text-sm rounded-xl lg:w-96">
                <button class="px-3 py-2 font-bold" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="relative py-12 lg:grid-cols-12 lg:grid">

        {{-- ラベル一覧（左） --}}
        <div class="absolute z-20 col-span-2 lg:block lg:static">
            <input type="checkbox" id="drawer-toggle" class="sr-only peer" checked>
            <label for="drawer-toggle" class="left-0 inline-block p-2 bg-indigo-500 rounded-lg lg:hidden top-40 ">
                <div class="w-6 h-1 mb-3 bg-white rounded-lg"></div>
                <div class="w-6 h-1 bg-white rounded-lg"></div>
            </label>
            <div class="hidden h-full bg-white shadow-lg rounded-r-2xl peer-checked:block lg:block">
                <div class="z-20 px-1 py-2 overflow-auto lg:static max-h-96 label-list-container">
                    {{-- ラベル表示 --}}
                    <div class="lg:col-span-2">
                        @livewire('web-book-label')
                        @livewire('label-list')
                        {{-- ラベル編集 --}}
                        <div>
                            @can('manager', $group_data)
                            <div>
                                <button onclick="Livewire.emit('showLabelEditModal')"
                                    class="flex items-center w-full gap-4 p-2 hover:bg-slate-100"><i
                                        class="lg:text-xl fa-solid fa-pencil fa-fw"></i>
                                    <p class="text-xs lg:text-sm">ラベルを編集</p>
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
        <div class="w-full mx-auto text-xs sm:px-6 lg:col-span-8 max-w-7xl lg:px-8">
            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">

                <x-flash-message status="suspension" />
                <x-flash-message status="error" />

                @foreach ($all_memos_data_paginated as $memo_data)
                @if ($memo_data['type'] == 0 )
                {{-- Webタイプ　の場合 --}}
                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4 ">
                            <div class="p-4">
                                <div
                                    class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md lg:px-8 rounded-2xl">
                                    <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                        {{-- 左側 --}}
                                        <div class="xl:col-span-3">
                                            <div class="flex items-center content-center">
                                                {{-- photo --}}
                                                @if($memo_data->profile_photo_path)
                                                <button
                                                    onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "
                                                    class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $memo_data->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "
                                                    class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full"></button>
                                                @endif
                                                {{-- メモ作成者情報 --}}
                                                <div>
                                                    <div class="mb-1 lg:mb-0">
                                                        <button
                                                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "
                                                            class="block ml-3 text-black">
                                                            {{ $memo_data['nickname'] }}
                                                        </button>
                                                        <button
                                                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "
                                                            class="ml-5 text-gray-500">
                                                            {{ $memo_data['username'] }}
                                                        </button>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>
                                                            {{ $memo_data['created_at']->format('Y-m-d') }}
                                                        </span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
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
                                                    class="font-bold text-left text-gray-700 break-all lg:text-sm title-font"
                                                    onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data['id'], 'type' => 'web']) }}' ">{{
                                                    $memo_data['title'] }}
                                                </button>
                                            </div>
                                            {{-- 『いいね』 『あとでよむ』 --}}
                                            <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3">
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
                                            @if (!$memo_data->labels->isEmpty())
                                            <div class="mt-8">
                                                @foreach ($memo_data->labels as $label)
                                                <div
                                                    class="inline-block px-3 py-1 m-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                                                    {{ $label->name }}</div>
                                                @endforeach
                                            </div>
                                            @endif

                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="flex flex-col xl:justify-between xl:col-span-3 xl:ml-2">
                                            {{-- shortMemo --}}
                                            <div class="flex">
                                                <p class="mb-3 leading-relaxed break-all">
                                                    {!! nl2br(e($memo_data['shortMemo'])) !!}
                                                </p>
                                            </div>

                                            {{-- ボタン --}}
                                            <div class="grid gap-10 px-10 pt-10 lg:px-0 lg:grid-cols-2 lg:gap-5">
                                                @if ($memo_data['user_id'] === Auth::id() )
                                                <button
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                    onclick="location.href='{{ route('group.memo_edit.edit', ['id' => $memo_data['id'], 'type' => 'web'] ) }}' ">編集する</button>
                                                @else
                                                <div class=""></div>
                                                @endif
                                                <button
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                    onclick="window.open('{{ $memo_data['url'] }}') ">リンクを開く</button>
                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="hidden xl:grid-cols-5 xl:grid">
                                            <div class="xl:col-span-2">
                                            </div>
                                            <div class="xl:col-span-3">
                                                <div class="text-right">
                                                    <i class="text-xl fas fa-globe"></i>
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
                    <div class="px-5 mx-auto">
                        <div class="-m-4 ">
                            <div class="p-4">
                                <div
                                    class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md lg:px-8 rounded-2xl">
                                    <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                        {{-- 左側 --}}
                                        <div class="xl:col-span-3">
                                            <div class="flex items-center content-center">
                                                {{-- photo --}}
                                                @if($memo_data->profile_photo_path)
                                                <button
                                                    onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "
                                                    class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $memo_data->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "
                                                    class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full"></button>
                                                @endif
                                                {{-- メモ作成者情報 --}}
                                                <div>
                                                    <div class="mb-1 lg:mb-0">
                                                        <button
                                                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "
                                                            class="block ml-3 text-black">
                                                            {{ $memo_data['nickname'] }}
                                                        </button>
                                                        <button
                                                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' "
                                                            class="ml-5 text-gray-500">
                                                            {{ $memo_data['username'] }}
                                                        </button>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>
                                                            {{ $memo_data['created_at']->format('Y-m-d') }}
                                                        </span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
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
                                                    class="font-bold text-left text-gray-700 break-all lg:text-sm title-font"
                                                    onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'type' => 'book'] ) }}' ">{{
                                                    $memo_data['title'] }}
                                                </button>
                                            </div>
                                            {{-- 『いいね』 『あとでよむ』 --}}
                                            <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3">
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
                                            @if (!$memo_data->labels->isEmpty())
                                            <div class="mt-8">
                                                @foreach ($memo_data->labels as $label)
                                                <div
                                                    class="inline-block px-3 py-1 m-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                                                    {{ $label->name }}</div>
                                                @endforeach
                                            </div>
                                            @endif


                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="flex flex-col justify-between xl:col-span-3 xl:ml-2">
                                            {{-- shortMemo --}}
                                            <div class="flex">
                                                <p class="mb-3 leading-relaxed break-all">
                                                    {!! nl2br(e($memo_data['shortMemo'])) !!}
                                                </p>
                                            </div>

                                            {{-- ボタン --}}
                                            <div class="grid gap-10 px-10 mt-6 lg:gap-5 lg:px-0 lg:grid-cols-2">
                                                @if ($memo_data['user_id'] === Auth::id() )
                                                <button
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500"
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
                                                        <i class="text-xl fas fa-book-open"></i>
                                                    </div>
                                                    <div class="flex justify-center">
                                                        @if($memo_data['book_photo_path'])
                                                        <img class="h-36 xl:h-auto"
                                                            src="{{ asset('storage/book-image/'. basename($memo_data['book_photo_path'])) }}" />
                                                        @endif
                                                    </div>
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

    {{-- ページに①通常アクセス or ②「戻るボタン」でアクセス　した際、 １：input 　２：テキストエリア と ３：セレクトボックス をリセットするための処理 --}}
    <script>
        function resetFormElements() {
                const selectElement = document.querySelector('select.max-w-xs');
                const inputElements = document.querySelectorAll('input:not([name="_token"])');
                const textareaElements = document.querySelectorAll('textarea');
                
                if (selectElement) {
                selectElement.value = '';
                }
                inputElements.forEach(input => {
                input.value = '';
                });
                textareaElements.forEach(textarea => {
                textarea.value = '';
                });
            }
            
            window.addEventListener('load', resetFormElements);


            //ラベル一覧モーダルにカーソルがある間、その他の部分がスクロールしないようにするための処理
            document.addEventListener('DOMContentLoaded', function() {
            const labelList = document.querySelector('.label-list-container');
            
            labelList.addEventListener('mouseenter', function() {
            document.body.style.overflow = 'hidden';
            });
            
            labelList.addEventListener('mouseleave', function() {
            document.body.style.overflow = 'auto';
            });
            });
    </script>

</div>