<div>
    <x-slot name="header">
        <div class="grid items-center grid-cols-4 lg:grid-cols-2">
            {{-- 左側 --}}
            <div class="flex w-auto col-span-3 text-xs lg:col-span-1">
                @if($user_data->profile_photo_path)
                <div class="flex-shrink-0 object-cover w-8 h-8 mr-3 bg-center rounded-full lg:w-10 lg:h-10">
                    <img class="object-fill w-8 h-8 rounded-full lg:w-10 lg:h-10"
                        src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                </div>
                @else
                <div class="flex-shrink-0 object-cover w-8 h-8 mr-3 bg-blue-200 bg-center rounded-full lg:w-10 lg:h-10">
                </div>
                @endif
                <div class="w-full">
                    <h2
                        class="overflow-hidden font-semibold leading-tight text-gray-800 whitespace-nowrap text-ellipsis">
                        {{ $user_data->nickname }}
                    </h2>
                    <div class="flex flex-col max-w-full">
                        <p class="ml-5 overflow-hidden text-gray-500 whitespace-nowrap text-ellipsis">
                            {{ $user_data->username }}
                        </p>
                        <p class="mt-3 lg:mt-0 lg:ml-28">{{ $count_all_memos_data }}<span class="ml-3">投稿</span>
                        </p>
                    </div>
                </div>
            </div>
            {{-- 右側 --}}
            <div class="flex items-center justify-end gap-5 lg:gap-20">

                <!-- 三点リーダー（モーダル） -->
                <div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                <i class="px-5 text-lg fas fa-ellipsis-v"></i>
                            </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                            <div class="flex flex-col text-gray-800">

                                <button onclick="Livewire.emit('showModalReportUser')"
                                    class="block w-full p-2 text-left hover:bg-slate-100">
                                    ユーザーを通報
                                </button>
                                <button type="button" class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に削除しますか？')) { @this.call('deleteUser') }">ユーザーを削除</button>

                                <button x-show="!isSuspended" type="button"
                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に利用停止にしますか？')) { @this.call('suspendUser') }">
                                    ユーザーを利用停止
                                </button>

                                <button x-show="isSuspended" type="button"
                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に利用停止解除しますか？')) { @this.call('liftSuspendUser') }">
                                    ユーザーを利用停止解除
                                </button>

                                <x-dropdown align="right">
                                    <x-slot name="trigger">
                                        <button
                                            class="flex w-full p-2 transition border-2 border-transparent focus:outline-none hover:bg-slate-100">
                                            <p>投げ銭する</p>
                                        </button>
                                    </x-slot>

                                    <!-- モーダルの中身 -->
                                    <x-slot name="content">
                                        <div class="flex flex-col pt-4 text-center text-gray-800 w-72">
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
                        </x-slot>
                    </x-dropdown>
                </div>


            </div>

            {{-- ユーザー通報モーダル --}}
            @livewire('report-user', ['user_id' => $user_data['id']])
        </div>
    </x-slot>

    <div class="lg:grid lg:grid-cols-12">
        <div class="w-full px-6 pt-12 mx-auto max-w-7xl lg:px-8 lg:col-start-3 lg:col-end-10">
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
        <div class="w-full mx-auto text-xs lg:col-span-8 max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">

                <x-flash-message status="quit" />

                @foreach ($all_memos_data_paginated as $memo_data)
                @if ($memo_data->type == 0)
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
                                                {{-- メモ作成者情報 --}}
                                                <div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>{{ $memo_data['created_at']->format('Y-m-d') }}</span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                        <span>{{ $memo_data['updated_at']->format('Y-m-d')}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- メモタイトル --}}
                                            <div class="mt-5 ml-3 leading-none y-4">
                                                <button
                                                    class="text-sm font-bold text-left text-gray-700 break-all title-font"
                                                    onclick="location.href='{{ route('group.memo_show',['memo_id' => $memo_data['id']]) }}' ">{{
                                                    $memo_data->title }}
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
                                            <div class="grid gap-10 px-10 pt-10 lg:grid-cols-2 lg:gap-5 lg:px-0">
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
                @elseif ($memo_data->type == 1)
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
                                                {{-- メモ作成者情報 --}}
                                                <div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>{{ $memo_data['created_at']->format('Y-m-d') }}</span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                        <span>{{ $memo_data['updated_at']->format('Y-m-d')}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- メモタイトル --}}
                                            <div class="mt-5 ml-3 leading-none y-4">
                                                <button
                                                    class="self-center text-sm font-bold text-gray-700 break-all title-font"
                                                    onclick="location.href='{{ route('group.memo_show',['memo_id'=>$memo_data['id']]) }}' ">{{
                                                    $memo_data->title }}
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
                                            <div class="grid gap-10 px-10 mt-6 lg:grid-cols-2 lg:gap-5 lg:px-0">
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

    {{-- ページに①通常アクセス or ②「戻るボタン」でアクセス　した際、 １：input（text）２：selectbox ３：textarea ４：checkbox ５：radiobutton をリセットするための処理
    --}}
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