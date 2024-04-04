<div x-data="{
    group: @entangle('show_groups'),
    suspension_group: @entangle('show_suspension_groups'),
}">
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            グループ一覧
        </h2>
    </x-slot>

    <div class="flex flex-col-reverse gap-8 px-6 pt-12 mx-auto lg:flex-row max-w-7xl lg:px-8">
        <!-- 検索、絞り込み -->
        <div>
            <form wire:submit.prevent="executeSearch">
                <input type="text" wire:model.defer="search" placeholder="グループ名かグループ紹介文のワードで検索"
                    class="w-64 text-sm rounded-xl sm:w-96">
                <button class="px-3 py-2 font-bold" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        {{-- ソート基準切り替え --}}
        <select wire:change="setSortCriteria($event.target.value)" class="max-w-xs text-sm rounded-xl"
            id="sortCriteria">
            <option value="report">通報数順</option>
            <option value="name">名前順</option>
        </select>

    </div>

    <div class="py-12">

        <div class="w-full px-6 mx-auto max-w-7xl lg:px-8 lg:col-span-8">
            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
                {{-- グループ / 利用停止中グループ 選択--}}
                <div class="mx-3 mb-10 border-b border-gray-400">
                    <div class="flex text-xs font-bold lg:text-sm lg:w-1/2">
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
                <div class="grid gap-10 text-xs" x-cloak x-show="group">
                    @foreach ($groups_data_paginated as $group_data)
                    <section class="w-full text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="flex flex-wrap justify-center -m-4">
                                <div class="w-full p-4">
                                    <div class="relative px-8 py-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                                        <div class="grid gap-10 lg:grid-cols-2 lg:gap-0">
                                            {{-- 左側 --}}
                                            <div>
                                                <div class="flex items-start content-center">
                                                    {{-- photo --}}
                                                    @if($group_data->group_photo_path)
                                                    <button type="button"
                                                        onclick="location.href='{{ route('admin.group_show', ['group_id' => $group_data->id]) }}' "
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img class="object-fill w-10 h-10 rounded-full"
                                                            src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                                                    </button>
                                                    @else
                                                    <button type="button"
                                                        onclick="location.href='{{ route('admin.group_show', ['group_id' => $group_data->id]) }}' "
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full">
                                                    </button>
                                                    @endif
                                                    {{-- end_photo --}}
                                                    <button class="flex self-center" type="button"
                                                        onclick="location.href='{{ route('admin.group_show', ['group_id' => $group_data->id]) }}' ">
                                                        <h1
                                                            class="text-sm font-bold text-left text-gray-700 title-font">
                                                            {{ $group_data->name }}
                                                        </h1>
                                                    </button>
                                                </div>
                                                <div class="mt-2 leading-none y-4">
                                                    <div
                                                        class="grid items-center gap-2 pt-5 leading-none text-gray-700 lg:ml-3">
                                                        <p>
                                                            管理者　：
                                                        </p>
                                                        <div class="grid gap-1 ml-3">
                                                            <p>
                                                                {{ $group_data->userRoles->first()->nickname }}
                                                            </p>
                                                            <p class="text-gray-500">
                                                                {{ $group_data->userRoles->first()->username }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <p class="items-center pt-5 leading-none text-gray-700 lg:ml-3">
                                                        通報数　：{{ $group_data->reports_count }}
                                                    </p>
                                                    <p class="items-center pt-5 leading-none text-gray-700 lg:ml-3">
                                                        メンバー：{{ $group_data->user_count }}
                                                    </p>
                                                    <p class="items-center pt-5 leading-none text-gray-700 lg:ml-3">
                                                        メモ　　：{{ $group_data->memos_count }}
                                                    </p>
                                                    <p class="items-center pt-5 leading-none text-gray-700 lg:ml-3">
                                                        コメント：{{ $group_data->comments_count }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12">
                                                {{-- 真ん中 --}}
                                                <div class="col-span-11 lg:ml-2">
                                                    <p class="mb-3 leading-relaxed break-words">
                                                        {!! nl2br(e($group_data->introduction)) !!}
                                                    </p>
                                                </div>
                                                {{-- 右側 三点リーダー（モーダル） --}}
                                                <div class="flex items-end content-end justify-end">
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
                <div class="grid gap-10 text-xs" x-cloak x-show="suspension_group">
                    @foreach ($suspension_groups_data_paginated as $group_data)
                    <section class="w-full text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="flex flex-wrap justify-center -m-4">
                                <div class="w-full p-4">
                                    <div class="relative px-8 py-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                                        <div class="grid gap-10 lg:grid-cols-2 lg:gap-0">
                                            {{-- 左側 --}}
                                            <div>
                                                <div class="flex items-start content-center">
                                                    {{-- photo --}}
                                                    @if($group_data->group_photo_path)
                                                    <button type="button"
                                                        onclick="location.href='{{ route('admin.group_show', ['group_id' => $group_data->id]) }}' "
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img class="object-fill w-10 h-10 rounded-full"
                                                            src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                                                    </button>
                                                    @else
                                                    <button type="button"
                                                        onclick="location.href='{{ route('admin.group_show', ['group_id' => $group_data->id]) }}' "
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full">
                                                    </button>
                                                    @endif
                                                    {{-- end_photo --}}
                                                    <button class="flex self-center" type="button"
                                                        onclick="location.href='{{ route('admin.group_show', ['group_id' => $group_data->id]) }}' ">
                                                        <h1
                                                            class="self-center text-sm font-bold text-left text-gray-700 title-font">
                                                            {{ $group_data->name }}
                                                        </h1>
                                                    </button>
                                                </div>
                                                <div class="mt-2 leading-none y-4">
                                                    <div
                                                        class="grid items-center gap-2 pt-5 leading-none text-gray-700 lg:ml-3">
                                                        <p>
                                                            管理者　：
                                                        </p>
                                                        <div class="grid gap-1 ml-3">
                                                            <p>
                                                                {{ $group_data->userRoles->first()->nickname }}
                                                            </p>
                                                            <p class="text-gray-500">
                                                                {{ $group_data->userRoles->first()->username }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <p class="items-center pt-5 leading-none text-gray-700 lg:ml-3">
                                                        通報数　：{{ $group_data->reports_count }}
                                                    </p>
                                                    <p class="items-center pt-5 leading-none text-gray-700 lg:ml-3">
                                                        メンバー：{{ $group_data->user_count }}
                                                    </p>
                                                    <p class="items-center pt-5 leading-none text-gray-700 lg:ml-3">
                                                        メモ　　：{{ $group_data->memos_count }}
                                                    </p>
                                                    <p class="items-center pt-5 leading-none text-gray-700 lg:ml-3">
                                                        コメント：{{ $group_data->comments_count }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12">
                                                {{-- 真ん中 --}}
                                                <div class="col-span-11 lg:ml-2">
                                                    <p class="mb-3 leading-relaxed break-words">
                                                        {!! nl2br(e($group_data->introduction)) !!}
                                                    </p>
                                                </div>
                                                {{-- 右側 三点リーダー（モーダル） --}}
                                                <div class="flex items-end content-end justify-end">
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

    {{-- ページに①通常アクセス or ②「戻るボタン」でアクセス　した際、 １：input（text）２：selectbox ３：textarea ４：checkbox ５：radiobutton をリセットするための処理
    --}}
    <script>
        function resetFormElements() {
                    const selectElement = document.querySelector('#sortCriteria');
                    const inputElements = document.querySelectorAll('input:not([name="_token"])');
                    const textareaElements = document.querySelectorAll('textarea');
                    
                    if (selectElement) {
                        selectElement.value = 'report';
                    }
                    inputElements.forEach(input => {
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            input.checked = false;
                        } else {
                            input.value = '';
                        }
                    });
                    textareaElements.forEach(textarea => {
                        textarea.value = '';
                    });
                }
                
                window.addEventListener('load', resetFormElements);
    </script>

</div>