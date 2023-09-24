<div x-data="{
    group: @entangle('show_groups'),
    suspension_group: @entangle('show_suspension_groups'),
}">
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
            グループ一覧
        </h2>
    </x-slot>

    <div class="flex flex-col-reverse gap-8 pt-12 mx-auto lg:flex-row max-w-7xl sm:px-6 lg:px-8">
        <!-- 検索、絞り込み -->
        <div>
            <form wire:submit.prevent="executeSearch">
                <input type="text" wire:model.defer="search" placeholder="グループ名かグループ紹介文のワードで検索" class="rounded-xl"
                    size="50">
                <button class="px-3 py-2 font-bold" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

    </div>

    <div class="py-12">

        <div class="w-full mx-auto max-w-7xl sm:px-6 lg:px-8 xl:col-span-8">
            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
                {{-- グループ / 利用停止中グループ 選択--}}
                <div class="mx-3 mb-10 border-b border-gray-400">
                    <div class="flex text-xs font-bold sm:text-lg lg:w-1/2">
                        <button
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            type="button" x-on:click="group = true; suspension_group=false;"
                            x-bind:class="group ? 'border-b-4 border-blue-300' :'' ">
                            <p>グループ</p>
                        </button>
                        <button
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            type="button" x-on:click="group = false; suspension_group=true;"
                            x-bind:class="suspension_group ? 'border-b-4 border-blue-300' :'' ">
                            <p>利用停止中グループ</p>
                        </button>
                    </div>
                </div>
                {{-- グループ　を　選択しているとき --}}
                <div class="grid gap-10" x-cloak x-show="group">
                    @foreach ($groups_data_paginated as $group_data)
                    <section class="w-full text-gray-600 body-font">
                        <div class="container px-5 mx-auto">
                            <div class="flex flex-wrap justify-center -m-4">
                                <div class="w-full p-4">
                                    <div class="relative px-8 py-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                                        <div class="grid gap-10 sm:grid-cols-2 sm:gap-0">
                                            {{-- 左側 --}}
                                            <div>
                                                <div class="flex items-start content-center">
                                                    {{-- photo --}}
                                                    @if($group_data->group_photo_path)
                                                    <div
                                                        class="flex-shrink-0 object-cover mr-3 bg-center rounded-full h-14 w-14">
                                                        <img class="object-fill rounded-full h-14 w-14"
                                                            src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                                                    </div>
                                                    @else
                                                    <div
                                                        class="flex-shrink-0 object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14">
                                                    </div>
                                                    @endif
                                                    {{-- end_photo --}}
                                                    <h1
                                                        class="self-center text-xl font-bold text-gray-700 title-font sm:text-2xl">
                                                        {{ $group_data->name }}
                                                    </h1>
                                                </div>
                                                <div class="mt-2 leading-none y-4 ">
                                                    <div
                                                        class="grid items-center grid-cols-2 pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        <p>
                                                            管理者　：{{ $group_data->userRoles->first()->nickname }}
                                                        </p>
                                                        <p class="text-gray-500">
                                                            {{ $group_data->userRoles->first()->username }}
                                                        </p>
                                                    </div>
                                                    <p
                                                        class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        通報数　：{{ $group_data->reports_count }}
                                                    </p>
                                                    <p
                                                        class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        メンバー：{{ $group_data->user_count }}
                                                    </p>
                                                    <p
                                                        class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        メモ　　：{{ $group_data->memos_count }}
                                                    </p>
                                                    <p
                                                        class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        コメント：{{ $group_data->comments_count }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12">
                                                {{-- 真ん中 --}}
                                                <div class="col-span-11">
                                                    <p class="mb-3 leading-relaxed">
                                                        {{ $group_data->introduction }}
                                                    </p>
                                                </div>
                                                {{-- 右側 三点リーダー（モーダル） --}}
                                                <div class="flex items-end content-end justify-end">
                                                    <x-dropdown align="right" width="48">
                                                        <x-slot name="trigger">
                                                            <button
                                                                class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                                                <i class="text-3xl fas fa-ellipsis-v"></i>
                                                            </button>
                                                        </x-slot>

                                                        <!-- モーダルの中身 -->
                                                        <x-slot name="content">
                                                            <div class="flex flex-col text-gray-800">

                                                                <button type="button"
                                                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                                                    onclick="
                                                                        let groupId = {{ $group_data->id }};
                                                                        if (confirm('本当に削除しますか？')) { @this.call('deleteGroup', groupId) }">
                                                                    グループを削除
                                                                </button>

                                                                <button type="button"
                                                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                                                    onclick="
                                                                        let groupId = {{ $group_data->id }};
                                                                        if (confirm('本当に利用停止にしますか？')) { @this.call('suspend', groupId) }">
                                                                    グループを利用停止
                                                                </button>
                                                            </div>
                                                        </x-slot>
                                                    </x-dropdown>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    @endforeach
                </div>

                {{-- 利用停止中グループ　を　選択しているとき --}}
                <div class="grid gap-10" x-cloak x-show="suspension_group">
                    @foreach ($suspension_groups_data_paginated as $group_data)
                    <section class="w-full text-gray-600 body-font">
                        <div class="container px-5 mx-auto">
                            <div class="flex flex-wrap justify-center -m-4">
                                <div class="w-full p-4">
                                    <div class="relative px-8 py-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                                        <div class="grid gap-10 sm:grid-cols-2 sm:gap-0">
                                            {{-- 左側 --}}
                                            <div>
                                                <div class="flex items-start content-center">
                                                    {{-- photo --}}
                                                    @if($group_data->group_photo_path)
                                                    <div
                                                        class="flex-shrink-0 object-cover mr-3 bg-center rounded-full h-14 w-14">
                                                        <img class="object-fill rounded-full h-14 w-14"
                                                            src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                                                    </div>
                                                    @else
                                                    <div
                                                        class="flex-shrink-0 object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14">
                                                    </div>
                                                    @endif
                                                    {{-- end_photo --}}
                                                    <h1
                                                        class="self-center text-xl font-bold text-gray-700 title-font sm:text-2xl">
                                                        {{ $group_data->name }}
                                                    </h1>
                                                </div>
                                                <div class="mt-2 leading-none y-4 ">
                                                    <div
                                                        class="grid items-center grid-cols-2 pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        <p>
                                                            管理者　：{{ $group_data->userRoles->first()->nickname }}
                                                        </p>
                                                        <p class="text-gray-500">
                                                            {{ $group_data->userRoles->first()->username }}
                                                        </p>
                                                    </div>
                                                    <p
                                                        class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        通報数　：{{ $group_data->reports_count }}
                                                    </p>
                                                    <p
                                                        class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        メンバー：{{ $group_data->user_count }}
                                                    </p>
                                                    <p
                                                        class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        メモ　　：{{ $group_data->memos_count }}
                                                    </p>
                                                    <p
                                                        class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                                                        コメント：{{ $group_data->comments_count }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12">
                                                {{-- 真ん中 --}}
                                                <div class="col-span-11">
                                                    <p class="mb-3 leading-relaxed">
                                                        {{ $group_data->introduction }}
                                                    </p>
                                                </div>
                                                {{-- 右側 三点リーダー（モーダル） --}}
                                                <div class="flex items-end content-end justify-end">
                                                    <x-dropdown align="right" width="48">
                                                        <x-slot name="trigger">
                                                            <button
                                                                class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                                                <i class="text-3xl fas fa-ellipsis-v"></i>
                                                            </button>
                                                        </x-slot>

                                                        <!-- モーダルの中身 -->
                                                        <x-slot name="content">
                                                            <div class="flex flex-col text-gray-800">

                                                                <button type="button"
                                                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                                                    onclick="
                                                                        let groupId = {{ $group_data->id }};
                                                                        if (confirm('本当に削除しますか？')) { @this.call('deleteGroup', groupId) }">
                                                                    グループを削除
                                                                </button>

                                                                <button type="button"
                                                                    class="block w-full p-2 text-left hover:bg-slate-100"
                                                                    onclick="
                                                                        let groupId = {{ $group_data->id }};
                                                                        if (confirm('本当に利用停止解除しますか？')) { @this.call('liftSuspend', groupId) }">
                                                                    グループを利用停止解除
                                                                </button>
                                                            </div>
                                                        </x-slot>
                                                    </x-dropdown>
                                                </div>
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

    {{-- グループ の ペジネーション --}}
    <div class="flex justify-center" x-cloak x-show="group">
        {{ $groups_data_paginated->withQueryString()->links() }}
    </div>

    {{-- 利用停止中グループ の ペジネーション --}}
    <div class="flex justify-center" x-cloak x-show="suspension_group">
        {{ $suspension_groups_data_paginated->withQueryString()->links() }}
    </div>

</div>