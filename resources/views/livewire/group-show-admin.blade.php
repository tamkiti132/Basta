<div x-data="{
    group_report: @entangle('show_group_reports'),
    member: @entangle('show_members'),
    user: @entangle('show_users'),
    suspension_user: @entangle('show_suspension_users'),
    user_pagination: @entangle('show_users_pagination'),
    suspension_user_pagination: @entangle('show_suspension_users_pagination'),
}" wire:init="$refresh">
    <x-slot name="header">
        <div class="grid grid-cols-4 lg:grid-cols-2">
            {{-- 左側 --}}
            <div class="flex w-auto col-span-3 lg:col-span-1">
                @if($group_data->group_photo_path)
                <button type="button"
                    class="flex-shrink-0 object-cover w-8 h-8 mr-3 bg-center rounded-full lg:w-10 lg:h-10"
                    onclick="location.href='{{ route('group.index', ['group_id' => $group_id]) }}' ">
                    <img class="object-fill w-8 h-8 rounded-full lg:w-10 lg:h-10"
                        src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                </button>
                @else
                <button type="button"
                    class="flex-shrink-0 object-cover w-8 h-8 mr-3 bg-blue-200 bg-center rounded-full lg:w-10 lg:h-10"
                    onclick="location.href='{{ route('group.index', ['group_id' => $group_id]) }}' "></button>
                @endif
                <div class="self-center w-full">
                    <button type="button" class="max-w-full"
                        onclick="location.href='{{ route('group.index', ['group_id' => $group_id]) }}' ">
                        <h2
                            class="overflow-hidden text-xs font-semibold leading-tight text-gray-800 lg:text-sm whitespace-nowrap text-ellipsis">
                            {{ $group_data->name }}
                        </h2>
                    </button>
                    <p class="ml-5 overflow-hidden text-xs text-gray-500 whitespace-nowrap text-ellipsis">
                        管理者：　{{ $group_data->userRoles->first()->nickname }}
                    </p>
                </div>
            </div>
            {{-- 右側 --}}
            <div class="flex items-center justify-end">
                <!-- 三点リーダー（モーダル） -->
                <div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                <i class="px-5 text-xl fas fa-ellipsis-v"></i>
                            </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                            <div class="flex flex-col text-gray-800" x-data="{ isSuspended: @entangle('isSuspended') }">


                                <button type="button" class="block w-full p-2 text-left hover:bg-slate-100"
                                    onclick="if (confirm('本当に削除しますか？')) { @this.call('deleteGroup') }">
                                    グループを削除
                                </button>

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
        </div>
    </x-slot>

    <div class="flex flex-col-reverse gap-8 px-6 pt-12 mx-auto lg:flex-row max-w-7xl lg:px-8">
        <!-- 検索、絞り込み -->
        <div>
            <form wire:submit.prevent="executeSearch">
                <input type="text" wire:model.defer="search" :placeholder="group_report ? 'ニックネームかユーザー名か本文のワードで検索' : 
                            (member ? 'ニックネームかユーザー名で検索' : '検索')" class="w-64 text-sm rounded-xl sm:w-96">
                <button class="px-3 py-2 font-bold" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <select wire:change="setReportReason($event.target.value)" class="max-w-xs text-sm rounded-xl"
            x-bind:class="{ 'hidden': member }">
            <option value="">通報理由で絞り込み</option>
            <option value="1">法律違反</option>
            <option value="2">不適切なコンテンツ</option>
            <option value="3">フィッシング or スパム</option>
            <option value="4">その他</option>
        </select>

        <select wire:change="setUserBlockState($event.target.value)" class="max-w-xs text-sm rounded-xl"
            x-bind:class="{ 'hidden': group_report }">
            <option value="">すべてのユーザー</option>
            <option value="1">ブロックなし</option>
            <option value="2">ブロック状態</option>
        </select>


    </div>

    <div class="py-12">

        {{-- メインコンテンツ（中央） --}}
        <div class="w-full mx-auto text-xs max-w-7xl lg:px-8 xl:col-span-8">
            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
                {{-- グループ通報情報 / メンバー 選択--}}
                <div class="mx-3 mb-10 border-b border-gray-400">
                    <div class="flex text-xs font-bold lg:text-sm lg:w-1/2">
                        <button
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            type="button"
                            x-on:click="group_report = true; member=false; user_pagination = false; suspension_user_pagination = false"
                            x-bind:class="group_report ? 'border-b-4 border-blue-300' :'' ">
                            <p>グループ通報情報</p>
                        </button>
                        <button wire:click="showMember"
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            x-bind:class="member ? 'border-b-4 border-blue-300' :'' ">
                            <p>メンバー</p>
                        </button>
                    </div>
                </div>
                {{-- グループ通報情報　を　選択しているとき --}}
                <div class="grid gap-10" x-cloak x-show="group_report">
                    @foreach ($group_reports_data_paginated as $group_report_data)
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4">
                                <div class="p-4">
                                    <div
                                        class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                                        <div class="grid w-full lg:grid-cols-12">
                                            {{-- 左側 --}}
                                            <div class="flex items-center content-center lg:col-span-8">
                                                {{-- photo --}}
                                                @if($group_report_data->contribute_user->profile_photo_path)
                                                <button class="object-cover w-10 h-10 mr-3 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show',['user_id'=>$group_report_data->contribute_user_id])  }}' ">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $group_report_data->contribute_user->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    class="object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show',['user_id' => $group_report_data->contribute_user_id]) }}' "></button>
                                                @endif
                                                {{-- コメント作成者情報 --}}
                                                <div>
                                                    <div>
                                                        <button class="block text-left text-black lg:ml-3" type="button"
                                                            onclick="location.href='{{ route('admin.user_show',['user_id' => $group_report_data->contribute_user_id]) }}' ">
                                                            {{ $group_report_data->contribute_user->nickname }}
                                                        </button>
                                                    </div>
                                                    <div class="grid items-center mt-1 text-gray-500 lg:ml-5">
                                                        <button class="text-left text-gray-500"
                                                            onclick="location.href='{{ route('admin.user_show',['user_id'=>$group_report_data->contribute_user_id]) }}' ">
                                                            {{ $group_report_data->contribute_user->username }}
                                                        </button>
                                                        <div>
                                                            <i class="fa-regular fa-clock"></i>
                                                            <span>
                                                                {{ $group_report_data->created_at->format('Y-m-d') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- 右側 --}}
                                            <div class="mt-5 lg:mt-0 lg:text-right lg:col-span-4">
                                                @if ($group_report_data->reason == 1)
                                                <p>法律違反</p>
                                                @elseif ($group_report_data->reason == 2)
                                                <p>不適切なコンテンツ</p>
                                                @elseif ($group_report_data->reason == 3)
                                                <p>フィッシング or スパム</p>
                                                @elseif ($group_report_data->reason == 4)
                                                <p>その他</p>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="grid grid-cols-12 mt-4">
                                            <div class="col-span-12">
                                                <p class="break-words">
                                                    {!! nl2br(e($group_report_data->detail)) !!}
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

                {{-- メンバー　を　選択しているとき --}}
                <div class="grid gap-10" x-cloak x-show="member">
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4 ">
                                <div class="p-4">
                                    <div
                                        class="grid gap-10 px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md lg:gap-7 rounded-2xl ">
                                        {{-- ユーザー / 利用停止中ユーザー --}}
                                        <div class="mb-2 border-b border-gray-400">
                                            <div class="flex text-xs font-bold lg:text-sm lg:w-1/2">
                                                <button
                                                    class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                                                    type="button"
                                                    x-on:click="user = true; user_pagination = true; suspension_user = false; suspension_user_pagination = false"
                                                    x-bind:class="user ? 'border-b-4 border-blue-300' :'' ">
                                                    ユーザー</p>
                                                </button>
                                                <button
                                                    class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                                                    type="button"
                                                    x-on:click="user = false; user_pagination = false; suspension_user = true; suspension_user_pagination = true"
                                                    x-bind:class="suspension_user ? 'border-b-4 border-blue-300' :'' ">
                                                    <p>利用停止中ユーザー</p>
                                                </button>
                                            </div>
                                        </div>
                                        {{-- 項目名 --}}
                                        <div class="items-center hidden grid-cols-12 lg:grid">
                                            {{-- プロフィール画像 ・ ニックネーム --}}
                                            <div class="flex items-center col-span-3">
                                                <p class="ml-12">ニックネーム</p>
                                            </div>
                                            {{-- ユーザーid --}}
                                            <div class="col-span-3">
                                                <p class="text-gray-500 ">
                                                    ユーザー名
                                                </p>
                                            </div>
                                            {{-- メールアドレス --}}
                                            <div class="col-span-2">
                                                <p class="text-gray-500 ">
                                                    メールアドレス
                                                </p>
                                            </div>
                                            {{-- 権限 --}}
                                            <div class="col-span-1">
                                                <p class="text-gray-500">
                                                    権限
                                                </p>
                                            </div>
                                            {{-- ユーザー通報 --}}
                                            <div class="grid col-span-2 grid-rows-2">
                                                <div class="text-center border-b-2 border-gray-300">
                                                    <p class="tracking-widest">通 報 数</p>
                                                </div>
                                                <div class="grid grid-cols-3 text-center">
                                                    <p>ユーザー</p>
                                                    {{-- 通報メモ --}}
                                                    <p>メモ</p>
                                                    {{-- 通報コメント --}}
                                                    <p>コメント</p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ユーザーの場合 --}}
                                        <div class="grid gap-10 lg:gap-7" x-cloak x-show="user">
                                            @foreach ($users_data_paginated as $user_data)
                                            {{-- １人分のまとまり --}}
                                            <div class="items-center lg:grid lg:grid-cols-12">
                                                {{-- プロフィール画像 ・ ニックネーム --}}
                                                <div class="flex items-center lg:col-span-3">
                                                    @if($user_data->profile_photo_path)
                                                    <button class="object-cover w-10 h-10 mr-3 bg-center rounded-full"
                                                        onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                        <img class="object-fill w-10 h-10 rounded-full"
                                                            src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                                                    </button>
                                                    @else
                                                    <button
                                                        class="object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full"
                                                        onclick="location.href='{{ route('admin.user_show',['user_id' => $user_data->id]) }}' "></button>
                                                    @endif
                                                    {{-- ニックネーム --}}
                                                    <button
                                                        onclick="location.href='{{ route('admin.user_show',['user_id' => $user_data->id]) }}' ">
                                                        {{ $user_data->nickname }}
                                                    </button>
                                                </div>
                                                {{-- ユーザーネーム --}}
                                                <div class="ml-16 lg:ml-0 lg:col-span-3">
                                                    <button class="text-gray-500"
                                                        onclick="location.href='{{ route('admin.user_show',['user_id' => $user_data->id]) }}' ">
                                                        {{ $user_data->username }}
                                                    </button>
                                                </div>
                                                {{-- メールアドレス --}}
                                                <div class="mt-3 lg:col-span-2 lg:mt-0">
                                                    <p class="text-gray-500">
                                                        {{ $user_data->email }}
                                                    </p>
                                                </div>
                                                {{-- 権限 --}}
                                                <div class="mt-3 lg:col-span-1 lg:mt-0">
                                                    <p class="text-gray-500">
                                                        @if ($user_data->groupRoles->first()->role === 10)
                                                    <p>管理者</p>
                                                    @elseif($user_data->groupRoles->first()->role === 50)
                                                    <p>サブ管理者</p>
                                                    @elseif($user_data->groupRoles->first()->role === 100)
                                                    <p>メンバー</p>
                                                    @endif
                                                    </p>
                                                </div>
                                                <div class="grid grid-cols-3 lg:col-span-3">
                                                    {{-- ユーザー通報 --}}
                                                    <div
                                                        class="grid grid-cols-3 col-span-2 mt-3 text-left lg:text-center lg:mt-0">
                                                        <div class="col-span-2 lg:hidden">
                                                            <p>ユーザー通報</p>
                                                            {{-- 通報メモ --}}
                                                            <p>通報メモ</p>
                                                            {{-- 通報コメント --}}
                                                            <p>通報コメント</p>
                                                        </div>
                                                        <div class="grid lg:items-center lg:grid-cols-3 lg:col-span-3">
                                                            <p><span class="mr-3 lg:hidden">：</span>
                                                                {{ $user_data->userReportsCount }}
                                                            </p>
                                                            <p><span class="mr-3 lg:hidden">：</span>
                                                                {{ $user_data->memoReportsCount }}
                                                            </p>
                                                            <p><span class="mr-3 lg:hidden">：</span>
                                                                {{ $user_data->commentReportsCount }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <!-- 三点リーダー（モーダル） -->
                                                    <div class="flex items-end justify-end col-span-1">
                                                        <x-dropdown align="right" width="48">
                                                            <x-slot name="trigger">
                                                                <button
                                                                    class="flex transition border-2 border-transparent focus:outline-none">
                                                                    <i class="text-lg fas fa-ellipsis-v"></i>
                                                                </button>
                                                            </x-slot>

                                                            <!-- モーダルの中身 -->
                                                            <x-slot name="content">
                                                                <div class="flex flex-col px-4 text-gray-800">
                                                                    <button type="button"
                                                                        class="block w-full p-2 text-left hover:bg-slate-100"
                                                                        onclick="
                                                                            let userId = {{ $user_data->id }};
                                                                            if (confirm('本当に削除しますか？')) { @this.call('deleteUser', userId) }">
                                                                        ユーザーを削除
                                                                    </button>

                                                                    <button type="button"
                                                                        class="block w-full p-2 text-left hover:bg-slate-100"
                                                                        onclick="
                                                                            let userId = {{ $user_data->id }};
                                                                            if (confirm('本当に利用停止にしますか？')) { @this.call('suspendUser', userId) }">
                                                                        ユーザーを利用停止
                                                                    </button>
                                                                </div>
                                                            </x-slot>
                                                        </x-dropdown>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                        {{-- 利用停止中ユーザーの場合 --}}
                                        <div class="grid gap-10 lg:gap-7" x-cloak x-show="suspension_user">
                                            @foreach ($suspension_users_data_paginated as $user_data)
                                            {{-- １人分のまとまり --}}
                                            <div class="items-center lg:grid lg:grid-cols-12">
                                                {{-- プロフィール画像 ・ ニックネーム --}}
                                                <div class="flex items-center lg:col-span-3">
                                                    @if($user_data->profile_photo_path)
                                                    <button class="object-cover w-10 h-10 mr-3 bg-center rounded-full"
                                                        onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                        <img class="object-fill w-10 h-10 rounded-full"
                                                            src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                                                    </button>
                                                    @else
                                                    <button
                                                        class="object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full"
                                                        onclick="location.href='{{ route('admin.user_show',['user_id' => $user_data->id]) }}' ">
                                                    </button>
                                                    @endif
                                                    <button class="text-gray-500 "
                                                        onclick="location.href='{{ route('admin.user_show',['user_id' => $user_data->id]) }}' ">
                                                        {{ $user_data->nickname }}
                                                    </button>
                                                </div>
                                                {{-- ユーザーネーム --}}
                                                <div class="ml-16 lg:ml-0 lg:col-span-3">
                                                    <button class="text-gray-500 "
                                                        onclick="location.href='{{ route('admin.user_show',['user_id' => $user_data->id]) }}' ">
                                                        {{ $user_data->username }}
                                                    </button>
                                                </div>
                                                {{-- メールアドレス --}}
                                                <div class="mt-3 lg:col-span-2 lg:mt-0">
                                                    <p class="text-gray-500 ">
                                                        {{ $user_data->email }}
                                                    </p>
                                                </div>
                                                {{-- 権限 --}}
                                                <div class="mt-3 lg:col-span-1 lg:mt-0">
                                                    <p class="text-gray-500 ">
                                                        @if ($user_data->groupRoles->first()->role === 10)
                                                    <p>管理者</p>
                                                    @elseif($user_data->groupRoles->first()->role === 50)
                                                    <p>サブ管理者</p>
                                                    @elseif($user_data->groupRoles->first()->role === 100)
                                                    <p>メンバー</p>
                                                    @endif
                                                    </p>
                                                </div>
                                                <div class="grid grid-cols-3 lg:col-span-3">
                                                    {{-- ユーザー通報 --}}
                                                    <div
                                                        class="grid grid-cols-3 col-span-2 mt-3 text-left lg:text-center lg:mt-0">
                                                        <div class="col-span-2 lg:hidden">
                                                            <p>ユーザー通報</p>
                                                            {{-- 通報メモ --}}
                                                            <p>通報メモ</p>
                                                            {{-- 通報コメント --}}
                                                            <p>通報コメント</p>
                                                        </div>
                                                        <div class="grid lg:items-center lg:grid-cols-3 lg:col-span-3">
                                                            <p><span class="mr-3 lg:hidden">：</span>
                                                                {{ $user_data->userReportsCount }}
                                                            </p>
                                                            <p><span class="mr-3 lg:hidden">：</span>
                                                                {{ $user_data->memoReportsCount }}
                                                            </p>
                                                            <p><span class="mr-3 lg:hidden">：</span>
                                                                {{ $user_data->commentReportsCount }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <!-- 三点リーダー（モーダル） -->
                                                    <div class="flex items-end justify-end col-span-1">
                                                        <x-dropdown align="right" width="48">
                                                            <x-slot name="trigger">
                                                                <button
                                                                    class="flex transition border-2 border-transparent focus:outline-none">
                                                                    <i class="text-lg fas fa-ellipsis-v"></i>
                                                                </button>
                                                            </x-slot>

                                                            <!-- モーダルの中身 -->
                                                            <x-slot name="content">
                                                                <div class="flex flex-col text-gray-800">
                                                                    <button type="button"
                                                                        class="block w-full p-2 text-left hover:bg-slate-100"
                                                                        onclick="
                                                                            let userId = {{ $user_data->id }};
                                                                            if (confirm('本当に削除しますか？')) { @this.call('deleteUser', userId) }">
                                                                        ユーザーを削除
                                                                    </button>

                                                                    <button type="button"
                                                                        class="block w-full p-2 text-left hover:bg-slate-100"
                                                                        onclick="
                                                                            let userId = {{ $user_data->id }};
                                                                            if (confirm('本当に利用停止解除しますか？')) { @this.call('liftSuspendUser', userId) }">
                                                                        ユーザーを利用停止解除
                                                                    </button>
                                                                </div>
                                                            </x-slot>
                                                        </x-dropdown>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>

    {{-- グループ通報情報 の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="group_report">
        {{ $group_reports_data_paginated->withQueryString()->links() }}
    </div>

    {{-- ユーザー の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="user_pagination">
        {{ $users_data_paginated->withQueryString()->links() }}
    </div>

    {{-- 利用停止中ユーザー の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="suspension_user_pagination">
        {{ $suspension_users_data_paginated->withQueryString()->links() }}
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