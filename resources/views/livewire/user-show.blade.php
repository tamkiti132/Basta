<div x-data="{
    user: @entangle('show_users'),
    memo: @entangle('show_memos'),
    comment: @entangle('show_comments'),
}" wire:init="$refresh">
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
                <div class="flex flex-col max-w-full">
                    <h2
                        class="overflow-hidden font-semibold leading-tight text-gray-800 whitespace-nowrap text-ellipsis">
                        {{ $user_data->nickname }}
                    </h2>
                    <p class="ml-5 overflow-hidden text-xs text-gray-500 whitespace-nowrap text-ellipsis">
                        {{ $user_data->username }}
                    </p>
                </div>
            </div>
            {{-- 右側 --}}
            <div class="flex items-center justify-end">
                <!-- 三点リーダー（モーダル） -->
                <div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex transition border-2 border-transparent focus:outline-none">
                                <i class="px-5 text-xl fas fa-ellipsis-v"></i>
                            </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                            <div class="flex flex-col text-gray-800" x-data="{ isSuspended: @entangle('isSuspended') }">


                                <button type="button" class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に削除しますか？')) { @this.call('deleteUser') }">
                                    ユーザーを削除
                                </button>

                                <button x-show="!isSuspended" type="button"
                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に利用停止にしますか？')) { @this.call('suspend') }">
                                    ユーザーを利用停止
                                </button>

                                <button x-show="isSuspended" type="button"
                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に利用停止解除しますか？')) { @this.call('liftSuspend') }">
                                    ユーザーを利用停止解除
                                </button>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

            </div>
        </div>
    </x-slot>

    <div class="flex flex-col-reverse gap-8 px-6 pt-12 mx-auto lg:flex-row max-w-7xl lg:px-8">
        <!-- 検索、絞り込み -->
        <div>
            <form wire:submit.prevent=" executeSearch">
                <input type="text" wire:model.defer="search" :placeholder="user ? 'ニックネームか本文のワードで検索' : 
                            (memo ? 'タイトルかメモ概要のワードで検索' : 
                            (comment ? '本文のワードで検索' : '検索'))" class="w-64 text-sm rounded-xl sm:w-96">
                <button class="px-3 py-2 font-bold" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <select wire:change="setReportReason($event.target.value)" class="max-w-xs text-sm rounded-xl"
            x-bind:class="{ 'hidden': memo || comment }">
            <option value="">通報理由で絞り込み</option>
            <option value="1">法律違反</option>
            <option value="2">不適切なコンテンツ</option>
            <option value="3">フィッシング or スパム</option>
            <option value="4">その他</option>
        </select>

        <select wire:change="setSortCriteria($event.target.value)" class="max-w-xs text-sm rounded-xl"
            x-bind:class="{ 'hidden': user }">
            <option value="report">通報数順</option>
            <option value="time">投稿日時順</option>
        </select>

        <select wire:change="setGroupId($event.target.value)" class="max-w-xs text-sm rounded-xl"
            x-bind:class="{ 'hidden': user }">
            <option value="">グループで絞り込み</option>
            @foreach($user_groups as $group)
            <option value=" {{ $group->id }}" {{ $group_id==$group->id ? 'selected' : '' }}>
                {{ $group->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="py-12 lg:grid-cols-12 lg:grid">
        {{-- ラベル一覧（左） --}}
        <div class="absolute z-20 col-span-2 lg:block lg:static"
            x-bind:class="{ 'invisible pointer-events-none': !memo }">
            <input type="checkbox" id="drawer-toggle" class="sr-only peer" checked>
            <label for="drawer-toggle" class="left-0 inline-block p-2 bg-indigo-500 rounded-lg lg:hidden top-40 ">
                <div class="w-6 h-1 mb-3 bg-white rounded-lg"></div>
                <div class="w-6 h-1 bg-white rounded-lg"></div>
            </label>
            <div class="hidden h-full bg-white shadow-lg rounded-r-2xl peer-checked:block lg:block">
                <div class="z-20 px-1 py-2 overflow-auto max-h-96 label-list-container lg:static">
                    {{-- ラベル表示 --}}
                    <div class="lg:col-span-2">

                        @livewire('web-book-label')

                        @livewire('label-list-mypage')

                    </div>
                </div>
            </div>
        </div>

        {{-- メインコンテンツ（中央） --}}
        <div class="w-full mx-auto text-xs max-w-7xl sm:px-6 lg:px-8 lg:col-span-8">
            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
                {{-- ユーザー通報情報 / メモ / コメント 選択--}}
                <div class="mx-3 mb-10 border-b border-gray-400">
                    <div class="flex text-xs font-bold lg:text-sm xl:w-1/2">
                        <button
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            type="button" x-on:click="user = true; memo=false; comment=false"
                            x-bind:class="user ? 'border-b-4 border-blue-300' :'' ">
                            <p>ユーザー通報情報</p>
                        </button>
                        <button
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            type="button" x-on:click="user = false; memo=true; comment=false"
                            x-bind:class="memo ? 'border-b-4 border-blue-300' :'' ">
                            <p>メモ</p>
                        </button>
                        <button
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            type="button" x-on:click="user = false; memo=false; comment=true"
                            x-bind:class="comment ? 'border-b-4 border-blue-300' :'' ">
                            <p>コメント</p>
                        </button>
                    </div>
                </div>
                {{-- ユーザー通報情報　を　選択しているとき --}}
                <div class="grid gap-10 text-xs" x-cloak x-show="user">
                    @foreach ($all_user_reports_data_paginated as $user_report_data)
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4">
                                <div class="p-4">
                                    <div
                                        class="relative px-4 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md lg:px-8 rounded-2xl">
                                        <div class="grid w-full lg:grid-cols-12">
                                            {{-- 左側 --}}
                                            <div class="flex items-center content-center lg:col-span-8">
                                                {{-- photo --}}
                                                @if($user_report_data->contribute_user->profile_photo_path)
                                                <button class="object-cover w-10 h-10 mr-3 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show',['user_id'=> $user_report_data->contribute_user_id]) }}' ">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $user_report_data->contribute_user->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    class="object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show',['user_id' => $user_report_data->contribute_user_id]) }}' "></button>
                                                @endif
                                                {{-- コメント作成者情報 --}}
                                                <div>
                                                    <div>
                                                        <button class="block text-left text-black lg:ml-3" type="button"
                                                            onclick="location.href='{{ route('admin.user_show',['user_id' => $user_report_data->contribute_user_id]) }}' ">
                                                            {{ $user_report_data->contribute_user->nickname }}
                                                        </button>
                                                    </div>
                                                    <div class="grid items-center mt-1 text-gray-500 lg:ml-5">
                                                        <button class="text-left text-gray-500"
                                                            onclick="location.href='{{ route('admin.user_show',['user_id' => $user_report_data->contribute_user_id]) }}' ">
                                                            {{ $user_report_data->contribute_user->username }}
                                                        </button>
                                                        <div>
                                                            <i class="fa-regular fa-clock"></i>
                                                            <span>
                                                                {{ $user_report_data->created_at->format('Y-m-d') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- 右側 --}}
                                            <div class="mt-5 lg:mt-0 lg:text-right lg:col-span-4">
                                                @if ($user_report_data->reason == 1)
                                                <p>法律違反</p>
                                                @elseif ($user_report_data->reason == 2)
                                                <p>不適切なコンテンツ</p>
                                                @elseif ($user_report_data->reason == 3)
                                                <p>フィッシング or スパム</p>
                                                @elseif ($user_report_data->reason == 4)
                                                <p>その他</p>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="grid grid-cols-12 mt-4">
                                            <div class="col-span-12">
                                                <p class="break-words">
                                                    {!! nl2br(e($user_report_data->detail)) !!}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    @endforeach
                </div>

                {{-- メモ　を　選択しているとき --}}
                <div class="grid gap-10 text-xs" x-cloak x-show="memo">
                    @foreach ($all_my_memos_data_paginated as $memo_data)
                    @if ($memo_data->type == 0)
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4">
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
                                                        class="font-bold text-left text-gray-700 break-all lg:text-sm"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid w-20 grid-cols-3 mt-5 ml-3 text-sm gap-14">
                                                    <div class="w-20">
                                                        <div class="inline">
                                                            <i class="fa-solid fa-bell" style="color: #c6c253;"></i>
                                                            <span class="ml-1">{{ $memo_data->reports_count }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data],
                                                        key('good-button-created1-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data],
                                                        key('later-read-button-created1-'.microtime(true)))
                                                    </div>
                                                </div>
                                                {{-- タグ --}}
                                                @if (!$memo_data->labels->isEmpty())
                                                <div class="mt-8 text-xs sm:text-sm">
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
                                                <div class="grid px-10 mt-10 text-center lg:px-0">
                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
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
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4">
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
                                                            <i class=" fa-regular fa-clock"></i>
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
                                                        class="font-bold text-left text-gray-700 break-all lg:text-sm title-font"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid w-20 grid-cols-3 mt-5 ml-3 gap-14">
                                                    <div class="w-20">
                                                        <div class="inline">
                                                            <i class="text-sm fa-solid fa-bell"
                                                                style="color: #c6c253;"></i>
                                                            <span class="ml-1">{{ $memo_data->reports_count }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data],
                                                        key('good-button-created2-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data],
                                                        key('later-read-button-created2-'.microtime(true)))
                                                    </div>
                                                </div>
                                                {{-- タグ --}}
                                                @if (!$memo_data->labels->isEmpty())
                                                <div class="mt-8 sm:text-sm">
                                                    @foreach ($memo_data->labels as $label)
                                                    <div
                                                        class="inline-block px-3 py-1 m-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                                                        {{ $label->name }}</div>
                                                    @endforeach
                                                </div>
                                                @endif


                                            </div>
                                            {{-- 真ん中 --}}
                                            <div class="xl:col-span-3 xl:ml-2">
                                                {{-- shortMemo --}}
                                                <div class="flex">
                                                    <p class="mb-3 leading-relaxed break-all">
                                                        {!! nl2br(e($memo_data['shortMemo'])) !!}
                                                    </p>
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

                {{-- コメント　を　選択しているとき --}}
                <div class="grid gap-10 text-xs" x-cloak x-show="comment">
                    @foreach ($comments_data_paginated as $comment_data)
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4">
                                <div class="p-4 cursor-pointer"
                                    onclick="linkToMemoOfComment('{{ route('group.memo_show', ['memo_id' => $comment_data->memo->id, 'group_id' => $comment_data->memo->group_id] ) }}')  ">
                                    <div
                                        class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                                        <div class="w-max">
                                            {{-- 左側 --}}
                                            <div class="items-center content-center">
                                                {{-- コメント作成者情報 --}}
                                                <div class="inline mt-1 ml-5 text-gray-500">
                                                    <i class="fa-regular fa-clock"></i>
                                                    <span>{{ $comment_data->created_at->format('Y-m-d')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-12 mt-4">
                                            <div class="col-span-11">
                                                <p>{!! nl2br(e($comment_data->comment)) !!}</p>
                                            </div>

                                            <script>
                                                function linkToMemoOfComment(url) {
                                                if (window.getSelection().toString() === '') {
                                                  window.location.href = url;
                                                }
                                              }
                                            </script>

                                            <!-- 右側 -->
                                            <div class="flex items-end justify-end">
                                                <i class="text-sm fa-solid fa-bell" style="color: #c6c253;"></i>
                                                <span class="ml-1">{{ $comment_data->reports_count }}</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ユーザー通報情報 の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="user">
        {{ $all_user_reports_data_paginated->withQueryString()->links() }}
    </div>

    {{-- メモ の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="memo">
        {{ $all_my_memos_data_paginated->withQueryString()->links() }}
    </div>

    {{-- コメント の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="comment">
        {{ $comments_data_paginated->withQueryString()->links() }}
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
                
                window.addEventListener('load', function() {
                        document.querySelectorAll('select.max-w-xs').forEach(select => {
                        select.selectedIndex = 0;
                    });
                });               
    </script>

</div>