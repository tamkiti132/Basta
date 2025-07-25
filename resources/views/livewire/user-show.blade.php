<div x-data="{
    user: true,
    memo: false,
    comment: false,
    showNextManagerModal: @entangle('showNextManagerModal'),
    showModalNobodyMember: @entangle('showModalNobodyMember'),
}" wire:init="$refresh">
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
                <div class="flex flex-col max-w-full">
                    <h2
                        class="overflow-hidden font-semibold leading-tight text-gray-800 whitespace-nowrap text-ellipsis">
                        {{ $user_data->nickname }}
                    </h2>
                    <p class="overflow-hidden ml-5 text-xs text-gray-500 whitespace-nowrap text-ellipsis">
                        {{ $user_data->username }}
                    </p>
                </div>
            </div>
            {{-- 右側 --}}
            <div class="flex justify-end items-center">
                <!-- 三点リーダー（モーダル） -->
                <div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex border-2 border-transparent transition focus:outline-none">
                                <i class="px-5 text-xl fas fa-ellipsis-v"></i>
                            </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                            <div class="flex flex-col text-gray-800" x-data="{ isSuspended: @entangle('isSuspended') }">


                                <button type="button" class="block p-2 w-full text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に削除しますか？')) { @this.call('isManager', {{ $user_data->id }}) }">
                                    ユーザーを削除
                                </button>

                                <button x-show="!isSuspended" type="button"
                                    class="block p-2 w-full text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に利用停止にしますか？')) { @this.call('suspend') }">
                                    ユーザーを利用停止
                                </button>

                                <button x-show="isSuspended" type="button"
                                    class="block p-2 w-full text-left hover:bg-slate-100"
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

    <div class="flex flex-col-reverse gap-8 px-6 pt-12 mx-auto max-w-7xl lg:flex-row lg:px-8">
        <!-- 検索、絞り込み -->
        <div>
            <input type="text" wire:model.debounce.100ms="search" :placeholder="user ? 'ニックネームか本文のワードで検索' :
                        (memo ? 'タイトルかメモ概要のワードで検索' :
                        (comment ? '本文のワードで検索' : '検索'))" class="w-64 text-sm rounded-xl sm:w-96">
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
            <label for="drawer-toggle" class="inline-block left-0 top-40 p-2 bg-indigo-500 rounded-lg lg:hidden">
                <div class="mb-3 w-6 h-1 bg-white rounded-lg"></div>
                <div class="w-6 h-1 bg-white rounded-lg"></div>
            </label>
            <div class="hidden h-full bg-white rounded-r-2xl shadow-lg peer-checked:block lg:block">
                <div class="overflow-auto z-20 px-1 py-2 max-h-96 label-list-container lg:static">
                    {{-- ラベル表示 --}}
                    <div class="lg:col-span-2">

                        @livewire('web-book-label')

                        @livewire('label-list-mypage')

                    </div>
                </div>
            </div>
        </div>

        {{-- メインコンテンツ（中央） --}}
        <div class="mx-auto w-full max-w-7xl text-xs sm:px-6 lg:px-8 lg:col-span-8">
            <div class="grid overflow-hidden gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">
                {{-- ユーザー通報情報 / メモ / コメント 選択--}}
                <div class="mx-3 mb-10 border-b border-gray-400">
                    <div class="flex text-xs font-bold lg:text-sm xl:w-1/2">
                        <button
                            class="w-1/2 text-center rounded-t-xl transition duration-700 ease-in-out hover:bg-blue-100"
                            type="button" x-on:click="user = true; memo=false; comment=false"
                            x-bind:class="user ? 'border-b-4 border-blue-300' :'' ">
                            <p>ユーザー通報情報</p>
                        </button>
                        <button
                            class="w-1/2 text-center rounded-t-xl transition duration-700 ease-in-out hover:bg-blue-100"
                            type="button" x-on:click="user = false; memo=true; comment=false"
                            x-bind:class="memo ? 'border-b-4 border-blue-300' :'' ">
                            <p>メモ</p>
                        </button>
                        <button
                            class="w-1/2 text-center rounded-t-xl transition duration-700 ease-in-out hover:bg-blue-100"
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
                                        class="overflow-hidden relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md lg:px-8">
                                        <div class="grid w-full lg:grid-cols-12">
                                            {{-- 左側 --}}
                                            <div class="flex content-center items-center lg:col-span-8">
                                                {{-- photo --}}
                                                @if($user_report_data->contribute_user->profile_photo_path)
                                                <button class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show',['user_id'=> $user_report_data->contribute_user_id]) }}' ">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $user_report_data->contribute_user->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show',['user_id' => $user_report_data->contribute_user_id]) }}' ">
                                                    <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                </button>
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
                                                        class="font-bold text-left text-gray-700 break-all lg:text-sm"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid grid-cols-3 gap-14 mt-5 ml-3 w-20 text-sm">
                                                    <div class="w-20">
                                                        <div class="inline">
                                                            <i class="fa-solid fa-bell" style="color: #c6c253;"></i>
                                                            <span class="ml-1">{{ $memo_data->reports_count }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-user-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-user-'.microtime(true)))
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
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
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
                                                        class="font-bold text-left text-gray-700 break-all lg:text-sm title-font"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid grid-cols-3 gap-14 mt-5 ml-3 w-20">
                                                    <div class="w-20">
                                                        <div class="inline">
                                                            <i class="text-sm fa-solid fa-bell"
                                                                style="color: #c6c253;"></i>
                                                            <span class="ml-1">{{ $memo_data->reports_count }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-user-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-user-'.microtime(true)))
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

                {{-- コメント　を　選択しているとき --}}
                <div class="grid gap-10 text-xs" x-cloak x-show="comment">
                    @foreach ($comments_data_paginated as $comment_data)
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4">
                                <div class="p-4 cursor-pointer"
                                    onclick="linkToMemoOfComment('{{ route('group.memo_show', ['memo_id' => $comment_data->memo->id, 'group_id' => $comment_data->memo->group_id] ) }}')  ">
                                    <div
                                        class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md sm:px-8">
                                        <div class="w-max">
                                            {{-- 左側 --}}
                                            <div class="content-center items-center">
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
                                            <div class="flex justify-end items-end">
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
