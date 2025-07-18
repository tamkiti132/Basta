<div x-data="{
    showNextManagerModal: @entangle('showNextManagerModal'),
    showModalNobodyMember: @entangle('showModalNobodyMember'),
 }">
    <x-slot name="header">
        <div class="grid grid-cols-4 items-center lg:grid-cols-2">
            {{-- 左側 --}}
            <div class="flex col-span-3 w-auto text-xs lg:col-span-1">
                @if($user_data->profile_photo_path)
                <div class="object-cover flex-shrink-0 mr-3 w-8 h-8 bg-center rounded-full lg:w-10 lg:h-10">
                    <img class="object-fill w-8 h-8 rounded-full lg:w-10 lg:h-10"
                        src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                </div>
                @else
                <div class="object-cover flex-shrink-0 mr-3 w-8 h-8 bg-center rounded-full lg:w-10 lg:h-10">
                    <img src="{{ asset('images/svg/default-user.svg') }}" />
                </div>
                @endif
                <div class="w-full">
                    <h2
                        class="overflow-hidden font-semibold leading-tight text-gray-800 whitespace-nowrap text-ellipsis">
                        {{ $user_data->nickname }}
                    </h2>
                    <div class="flex flex-col max-w-full">
                        <p class="overflow-hidden ml-5 text-gray-500 whitespace-nowrap text-ellipsis">
                            {{ $user_data->username }}
                        </p>
                        <p class="mt-3 lg:mt-0 lg:ml-28">{{ $count_all_memos_data }}<span class="ml-3">投稿</span>
                        </p>
                    </div>
                </div>
            </div>
            {{-- 右側 --}}
            <div class="flex gap-5 justify-end items-center lg:gap-20">

                <!-- 三点リーダー（モーダル） -->
                <div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex text-sm border-2 border-transparent transition focus:outline-none">
                                <i class="px-5 text-lg fas fa-ellipsis-v"></i>
                            </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                            <div class="flex flex-col text-gray-800" x-data="{ isSuspended: @entangle('isSuspended') }">

                                <button onclick="Livewire.emit('showModalReportUser')"
                                    class="block p-2 w-full text-left hover:bg-slate-100">
                                    ユーザーを通報
                                </button>

                                @can('admin-higher')
                                <button type="button" class="block p-2 w-full text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に削除しますか？')) { @this.call('isManager', {{ $user_data->id }}) }">ユーザーを削除</button>

                                {{-- @if (!$isSuspended) --}}
                                <button x-show="!isSuspended" type="button"
                                    class="block p-2 w-full text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に利用停止にしますか？')) { @this.call('suspendUser') }">
                                    ユーザーを利用停止
                                </button>
                                {{-- @else --}}
                                <button x-show="isSuspended" type="button"
                                    class="block p-2 w-full text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に利用停止解除しますか？')) { @this.call('liftSuspendUser') }">
                                    ユーザーを利用停止解除
                                </button>
                                {{-- @endif --}}


                                @endcan

                                <!-- TODO: 投げ銭機能（あとでやる） -->
                                {{-- @if (auth()->id() !== $user_data->id)
                                    <button type="button" class="block p-2 w-full text-left hover:bg-slate-100"
                                    onclick="if (confirm('100円の投げ銭をしますか？')) {  }">
                                    投げ銭する
                                    </button>
                                @endif --}}

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
        <div class="px-6 pt-12 mx-auto w-full max-w-7xl lg:px-8 lg:col-start-3 lg:col-end-10">
            <input type="text" wire:model.debounce.100ms="search" placeholder="タイトルかメモ概要のワードで検索"
                class="w-64 text-sm rounded-xl lg:w-96">
        </div>
    </div>

    <div class="relative py-12 lg:grid-cols-12 lg:grid">
        {{-- ラベル一覧（左） --}}
        <div class="absolute z-20 col-span-2 lg:block lg:static">
            <input type="checkbox" id="drawer-toggle" class="sr-only peer" checked>
            <label for="drawer-toggle" class="inline-block left-0 top-40 p-2 bg-indigo-500 rounded-lg lg:hidden">
                <div class="mb-3 w-6 h-1 bg-white rounded-lg"></div>
                <div class="w-6 h-1 bg-white rounded-lg"></div>
            </label>
            <div class="hidden h-full bg-white rounded-r-2xl shadow-lg peer-checked:block lg:block">

                <div class="overflow-auto z-20 px-1 py-2 max-h-96 lg:static label-list-container">
                    {{-- ラベル表示 --}}
                    <div class="lg:col-span-2">

                        @livewire('web-book-label')

                        @livewire('label-list')

                        {{-- ラベル編集 --}}
                        <div>
                            @can('manager', $group_data)
                            <div>
                                <button onclick="Livewire.emit('showLabelEditModal')"
                                    class="flex gap-4 items-center p-2 w-full hover:bg-slate-100"><i
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
        <div class="mx-auto w-full max-w-7xl text-xs lg:col-span-8 sm:px-6 lg:px-8">
            <div class="grid overflow-hidden gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">

                @foreach ($all_memos_data_paginated as $memo_data)
                @if ($memo_data->type == 0)
                {{-- Webタイプ　の場合 --}}
                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md lg:px-8">
                                    <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                        {{-- 左側 --}}
                                        <div class="xl:col-span-3">
                                            <div class="flex content-center items-center">
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
                                            <div class="grid grid-cols-2 gap-10 mt-5 ml-3 w-20">
                                                <div class="w-20">
                                                    @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-'.microtime(true)))
                                                </div>
                                                <div class="w-20">
                                                    @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-'.microtime(true)))
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
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 lg:px-1 focus:outline-none hover:bg-indigo-500"
                                                    onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'web'] ) }}' ">編集する</button>
                                                @else
                                                <div class=""></div>
                                                @endif

                                                <button
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 lg:px-1 focus:outline-none hover:bg-indigo-500"
                                                    onclick="window.open('{{ $memo_data->web_type_feature->url }}') ">リンクを開く</button>
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
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md lg:px-8">
                                    <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                        {{-- 左側 --}}
                                        <div class="xl:col-span-3">
                                            <div class="flex content-center items-center">
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
                                            <div class="grid grid-cols-2 gap-10 mt-5 ml-3 w-20">
                                                <div class="w-20">
                                                    @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-'.microtime(true)))
                                                </div>
                                                <div class="w-20">
                                                    @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-'.microtime(true)))
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
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 lg:px-1 focus:outline-none hover:bg-indigo-500"
                                                    onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'book'] ) }}' ">編集する</button>
                                                @else
                                                <div class=""></div>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="grid grid-cols-5">
                                            <div class="col-span-5">
                                                <div class="m-auto max-w-xs">
                                                    <div class="hidden text-right xl:block">
                                                        <i class="text-xl fas fa-book-open"></i>
                                                    </div>
                                                    <div class="flex justify-center">
                                                        @if($memo_data->book_type_feature?->book_photo_path)
                                                        <img class="h-36 xl:h-auto"
                                                            src="{{ asset('storage/book-image/'. basename($memo_data->book_type_feature->book_photo_path)) }}" />
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

    {{-- 次の管理者選択モーダル --}}
    <div x-cloak x-show="showNextManagerModal"
        class="flex fixed top-0 left-0 z-40 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
        <div class="flex flex-col justify-center px-3 py-2 w-full max-w-xl h-auto bg-white rounded-xl"
            x-on:click.away="$wire.closeModal"
            >

            @if($targetGroup)

            <p>{{ $selectedNextManagerCount + 1 }} / {{ $totalManagedGroupCount }}</p>

            <div class="flex flex-col items-center pb-2 mb-6">
                @if($targetGroup->group_photo_path)
                    <div class="object-cover mr-3 w-8 h-8 bg-center rounded-full">
                        <img class="object-fill w-8 h-8 rounded-full"
                            src="{{ asset('storage/group-image/'. $targetGroup->group_photo_path) }}" />
                    </div>
                @else
                    <div class="object-cover mr-3 w-8 h-8 bg-blue-200 bg-center rounded-full"></div>
                @endif
                <p>{{ $targetGroup->name }}</p>
            </div>

            <div class="flex justify-center mb-6 text-sm font-bold text-center">
                <p class="leading-relaxed">
                    @if($fragSubManagerOrMember == 'subManager')
                    <span class="text-blue-600">サブ管理者</span>の中から、<br>
                    次の管理者を選択してください
                    @elseif($fragSubManagerOrMember == 'member')
                    サブ管理者がいないため、<br>
                    <span class="text-green-600">メンバー</span>の中から、<br>
                    次の管理者を選択してください
                    @endif
                </p>
            </div>

            <form wire:submit.prevent="selectNextManager" class="flex flex-col p-2">

                <select class="p-2 mb-4 w-full rounded border border-gray-300" required wire:model.defer="nextManagerId">
                    <option value="" disabled>次の管理者を選択してください</option>
                    @foreach ($targetGroup->userRoles as $user_data)
                    <option value="{{ $user_data->id }}" wire:key="user_role_option_{{ $user_data->id }}">
                        {{ $user_data->nickname }} ( {{ $user_data->username }} )
                    </option>
                    @endforeach
                </select>

                <div class="flex gap-4 justify-end pt-2">
                    <button type="button" class="px-1 py-2 w-28 border border-gray-300 hover:bg-slate-100"
                        x-on:click="$wire.closeModal">キャンセル</button>
                    <button type="submit"
                        class="px-1 py-2 w-28 text-red-500 border border-red-500 hover:bg-red-50">決定</button>
                </div>
            </form>
            @endif

        </div>
    </div>

    {{-- メンバーがいない場合のモーダル --}}
    <div x-cloak x-show="showModalNobodyMember"
        class="flex fixed top-0 left-0 z-40 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
        <div class="flex flex-col justify-center px-3 py-2 w-full max-w-xl h-auto bg-white rounded-xl"
            x-on:click.away="$wire.closeModal"
            >

            @if($targetGroup)

            <p>{{ $selectedNextManagerCount + 1 }} / {{ $totalManagedGroupCount }}</p>

            <div class="flex flex-col items-center pb-2 mb-6">
                @if($targetGroup->group_photo_path)
                    <div class="object-cover mr-3 w-8 h-8 bg-center rounded-full">
                        <img class="object-fill w-8 h-8 rounded-full"
                            src="{{ asset('storage/group-image/'. $targetGroup->group_photo_path) }}" />
                    </div>
                @else
                    <div class="object-cover mr-3 w-8 h-8 bg-blue-200 bg-center rounded-full"></div>
                @endif
                <p>{{ $targetGroup->name }}</p>
            </div>

            <div class="flex justify-center mb-6 text-sm font-bold text-center">
                <p class="leading-relaxed">
                    このグループにはメンバーがいません。<br>
                    このグループを削除しますか？
                </p>
            </div>

            <div class="flex flex-col p-2">
                <div class="flex gap-4 justify-end pt-2">
                    <button type="button" class="px-1 py-2 w-28 border border-gray-300 hover:bg-slate-100"
                        x-on:click="$wire.closeModal">キャンセル</button>
                    <button type="button"
                        class="px-1 py-2 w-28 text-red-500 border border-red-500 hover:bg-red-50"
                        x-on:click="$wire.addDeleteGroupFlag">削除</button>
                </div>
            </div>
            @endif

        </div>
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

    <script>
        document.addEventListener('livewire:load', function () {
            // confirmDeletionイベントのリスナー
            Livewire.on('confirmDeletion', () => {
                if (confirm('一連の処理を実行してよろしいですか？')) {
                    Livewire.emit('deleteUser');
                } else {
                    Livewire.emit('closeModal');
                }
            });
        });
    </script>

</div>
