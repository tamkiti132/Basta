<div x-data="{
    user: @entangle('show_user'),
    suspended_user: @entangle('show_suspended_user'),
    showNextManagerModal: @entangle('showNextManagerModal'),
    showModalNobodyMember: @entangle('showModalNobodyMember'),
}">
    {{-- <div class="fixed inset-0 z-40 bg-gray-100 bg-opacity-40">
        <div class="absolute inset-0 m-auto w-10 h-10 bg-red-500"></div>
    </div> --}}
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            ユーザー一覧
        </h2>
    </x-slot>

    <div class="flex flex-col-reverse gap-8 px-6 pt-12 mx-auto max-w-7xl lg:flex-row lg:px-8">
        <!-- 検索、絞り込み -->
        <div>
            <input type="text" wire:model.debounce.100ms="search" placeholder="ニックネームかユーザー名で検索"
                class="w-64 text-sm rounded-xl sm:w-96">
        </div>

        {{-- ソート基準切り替え --}}
        <select wire:change="setSortCriteria($event.target.value)" class="max-w-xs text-sm rounded-xl"
            id="sortCriteria">
            <option value="report_all">通報数順（全ての合計）</option>
            <option value="report_user">通報数順（ユーザー）</option>
            <option value="report_memo">通報数順（メモ）</option>
            <option value="report_comment">通報数順（コメント）</option>
            <option value="nickname">名前順</option>
        </select>

    </div>

    <div class="py-12">
        <div class="mx-auto max-w-7xl lg:px-6">
            <div class="grid overflow-hidden gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">

                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="grid gap-10 px-8 pt-8 pb-8 text-xs bg-gray-100 bg-opacity-75 rounded-2xl shadow-md lg:gap-7">
                                    {{-- ユーザー / 利用停止中ユーザー --}}
                                    <div class="mb-2 border-b border-gray-400">
                                        <div class="flex text-xs font-bold lg:text-sm lg:w-1/2">
                                            <button
                                                class="w-1/2 text-center rounded-t-xl transition duration-700 ease-in-out hover:bg-blue-100"
                                                type="button" x-on:click="user = true; suspended_user = false"
                                                x-bind:class="user ? 'border-b-4 border-blue-300' :'' ">
                                                <p>ユーザー</p>
                                            </button>
                                            <button
                                                class="w-1/2 text-center rounded-t-xl transition duration-700 ease-in-out hover:bg-blue-100"
                                                type="button" x-on:click="user = false; suspended_user = true"
                                                x-bind:class="suspended_user ? 'border-b-4 border-blue-300' :'' ">
                                                <p>利用停止中ユーザー</p>
                                            </button>
                                        </div>
                                    </div>
                                    {{-- 項目名 --}}
                                    <div class="hidden grid-cols-12 items-center text-xs lg:grid">
                                        {{-- プロフィール画像 ・ ニックネーム --}}
                                        <div class="flex col-span-3 items-center">
                                            <p class="ml-12">ニックネーム</p>
                                        </div>
                                        {{-- ユーザーid --}}
                                        <div class="col-span-3">
                                            <p class="text-gray-500">
                                                ユーザー名
                                            </p>
                                        </div>
                                        {{-- メールアドレス --}}
                                        <div class="col-span-3">
                                            <p class="text-gray-500">
                                                メールアドレス
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
                                    <div class="grid gap-10 text-xs lg:gap-7" x-cloak x-show="user">
                                        @foreach ($all_not_suspended_users_data_paginated as $user_data)
                                        {{-- １人分のまとまり --}}
                                        <div wire:key='{{ "not_suspend". $user_data->id }}'
                                            class="items-center lg:grid lg:grid-cols-12">
                                            {{-- プロフィール画像 ・ ニックネーム --}}
                                            <div class="flex items-center lg:col-span-3">
                                                @if($user_data->profile_photo_path)
                                                <button class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                    <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                </button>
                                                @endif

                                                <button
                                                    onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                    {{ $user_data->nickname }}
                                                </button>
                                            </div>
                                            {{-- ユーザーid --}}
                                            <div class="ml-16 lg:ml-0 lg:col-span-3">
                                                <button class="text-gray-500"
                                                    onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                    {{ $user_data->username }}
                                                </button>
                                            </div>
                                            {{-- メールアドレス --}}
                                            <div class="mt-3 lg:col-span-3 lg:mt-0">
                                                <p class="text-gray-500">
                                                    {{ $user_data->email }}
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
                                                        <p><span class="mr-3 lg:hidden">：</span>{{
                                                            $user_data->userReportsCount }}</p>
                                                        <p><span class="mr-3 lg:hidden">：</span>{{
                                                            $user_data->memoReportsCount }}</p>
                                                        <p><span class="mr-3 lg:hidden">：</span>{{
                                                            $user_data->commentReportsCount }}</p>
                                                    </div>
                                                </div>
                                                <!-- 三点リーダー（モーダル） -->
                                                <div class="flex col-span-1 justify-end items-end">
                                                    <x-dropdown align="right" width="48">
                                                        <x-slot name="trigger">
                                                            <button
                                                                class="flex border-2 border-transparent transition focus:outline-none">
                                                                <i class="text-lg fas fa-ellipsis-v"></i>
                                                            </button>
                                                        </x-slot>

                                                        <!-- モーダルの中身 -->
                                                        <x-slot name="content">
                                                            <div class="flex flex-col px-4 text-gray-800">
                                                                <button type="submit"
                                                                    class="block p-2 w-full text-left hover:bg-slate-100"
                                                                    onclick="if (confirm('本当に削除しますか？')) { @this.call('isManager', {{ $user_data->id }}) }">ユーザーを削除</button>

                                                                <button type="submit"
                                                                    class="block p-2 w-full text-left hover:bg-slate-100"
                                                                    onclick="if (confirm('本当に利用停止にしますか？')) { @this.call('suspendUser', {{ $user_data->id }}) }">ユーザーを利用停止</button>
                                                            </div>
                                                        </x-slot>
                                                    </x-dropdown>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- 利用停止中ユーザーの場合 --}}
                                    <div class="grid gap-10 text-xs lg:gap-7" x-cloak x-show="suspended_user">
                                        @foreach ($all_suspended_users_data_paginated as $user_data)
                                        {{-- １人分のまとまり --}}
                                        <div wire:key='{{ "suspend". $user_data->id }}'
                                            class="items-center lg:grid lg:grid-cols-12">
                                            {{-- プロフィール画像 ・ ニックネーム --}}
                                            <div class="flex items-center lg:col-span-3">
                                                @if($user_data->profile_photo_path)
                                                <button class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                    onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                    <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                </button>
                                                @endif
                                                <button
                                                    onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                    {{ $user_data->nickname }}
                                                </button>
                                            </div>
                                            {{-- ユーザーid --}}
                                            <div class="ml-16 lg:ml-0 lg:col-span-3">
                                                <button class="text-gray-500"
                                                    onclick="location.href='{{ route('admin.user_show', ['user_id' => $user_data->id]) }}' ">
                                                    {{ $user_data->username }}
                                                </button>
                                            </div>
                                            {{-- メールアドレス --}}
                                            <div class="mt-3 lg:col-span-3 lg:mt-0">
                                                <p class="text-gray-500">
                                                    {{ $user_data->email }}
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
                                                    <div class="grid lg:grid-cols-3 lg:col-span-3 lg:items-center">
                                                        <p><span class="mr-3 lg:hidden">：</span>{{
                                                            $user_data->userReportsCount }}</p>
                                                        <p><span class="mr-3 lg:hidden">：</span>{{
                                                            $user_data->memoReportsCount }}</p>
                                                        <p><span class="mr-3 lg:hidden">：</span>{{
                                                            $user_data->commentReportsCount }}</p>
                                                    </div>
                                                </div>
                                                <!-- 三点リーダー（モーダル） -->
                                                <div class="flex col-span-1 justify-end items-end">
                                                    <x-dropdown align="right" width="48">
                                                        <x-slot name="trigger">
                                                            <button
                                                                class="flex border-2 border-transparent transition focus:outline-none">
                                                                <i class="text-lg fas fa-ellipsis-v"></i>
                                                            </button>
                                                        </x-slot>

                                                        <!-- モーダルの中身 -->
                                                        <x-slot name="content">
                                                            <div class="flex flex-col text-gray-800">
                                                                <button type="submit"
                                                                    class="block p-2 w-full text-left hover:bg-slate-100"
                                                                    onclick="if (confirm('本当に削除しますか？')) { @this.call('isManager', {{ $user_data->id }}) }">ユーザーを削除</button>

                                                                <button type="submit"
                                                                    class="block p-2 w-full text-left hover:bg-slate-100"
                                                                    onclick="if (confirm('本当に利用停止解除しますか？')) { @this.call('liftSuspendUser', {{ $user_data->id }}) }">ユーザーを利用停止解除</button>
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

    {{-- メンバー の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="user">
        {{ $all_not_suspended_users_data_paginated->withQueryString()->links() }}
    </div>

    {{-- 利用停止中メンバー の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="suspended_user">
        {{ $all_suspended_users_data_paginated->withQueryString()->links() }}
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
                    const selectElement = document.querySelector('#sortCriteria');
                    const inputElements = document.querySelectorAll('input:not([name="_token"])');
                    const textareaElements = document.querySelectorAll('textarea');

                    if (selectElement) {
                        selectElement.value = 'report_all';
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
