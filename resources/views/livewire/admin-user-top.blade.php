<div x-data="{
    user: @entangle('show_users'),
    suspension_user: @entangle('show_suspension_users'),
}">
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            運営ユーザー一覧
        </h2>
    </x-slot>

    <div class="flex flex-col-reverse gap-8 px-6 pt-12 mx-auto lg:flex-row max-w-7xl lg:px-8">
        <!-- 検索、絞り込み -->
        <div>
            <form wire:submit.prevent="executeSearch">
                <input type="text" wire:model.defer="search" placeholder="ニックネームかユーザー名で検索"
                    class="w-64 text-sm rounded-xl sm:w-96">
                <button class="px-3 py-2 font-bold" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="py-12">
        <div class="mx-auto text-xs max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">

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
                                                type="button" x-on:click="user = true; suspension_user = false"
                                                x-bind:class="user ? 'border-b-4 border-blue-300' :'' ">
                                                ユーザー</p>
                                            </button>
                                            <button
                                                class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                                                type="button" x-on:click="user = false; suspension_user = true"
                                                x-bind:class="suspension_user ? 'border-b-4 border-blue-300' :'' ">
                                                <p>利用停止中ユーザー</p>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- ユーザーの場合 --}}
                                    <div class="grid gap-10 lg:gap-7" x-cloak x-show="user">
                                        @foreach ($all_not_suspended_users_data_paginated as $user_data)
                                        {{-- １人分のまとまり --}}
                                        <div class="items-center lg:grid lg:grid-cols-12">
                                            {{-- プロフィール画像 ・ ニックネーム --}}
                                            <div class="flex items-center lg:col-span-3">
                                                @if($user_data->profile_photo_path)
                                                <div class="object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                                                </div>
                                                @else
                                                <div
                                                    class="object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full">
                                                </div>
                                                @endif
                                                <p>{{ $user_data->nickname }}</p>
                                            </div>
                                            {{-- ユーザーid --}}
                                            <div class="ml-16 lg:ml-0 lg:col-span-3">
                                                <div class="text-gray-500 ">
                                                    {{ $user_data->username }}
                                                </div>
                                            </div>
                                            {{-- メールアドレス --}}
                                            <div class="mt-3 lg:col-span-3 lg:mt-0">
                                                <p class="text-gray-500 ">
                                                    {{ $user_data->email }}
                                                </p>
                                            </div>


                                            <!-- 三点リーダー（モーダル） -->
                                            <div class="flex items-end justify-end col-span-3">
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
                                                                let user_id = {{ $user_data->id }};
                                                                if (confirm('本当に削除しますか？')) { @this.call('deleteUser', user_id) }">
                                                                ユーザーを削除
                                                            </button>
                                                            <button type="button"
                                                                class="block w-full p-2 text-left hover:bg-slate-100"
                                                                onclick="
                                                                let user_id = {{ $user_data->id }};
                                                                if (confirm('本当に利用停止にしますか？')) { @this.call('suspendUser', user_id) }">
                                                                ユーザーを利用停止
                                                            </button>
                                                        </div>
                                                    </x-slot>
                                                </x-dropdown>
                                            </div>

                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- 利用停止中ユーザーの場合 --}}
                                    <div class="grid gap-10 lg:gap-7" x-cloak x-show="suspension_user">
                                        @foreach ($all_suspended_users_data_paginated as $user_data)
                                        {{-- １人分のまとまり --}}
                                        <div class="items-center lg:grid lg:grid-cols-12">
                                            {{-- プロフィール画像 ・ ニックネーム --}}
                                            <div class="flex items-center lg:col-span-3">
                                                @if($user_data->profile_photo_path)
                                                <div class="object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                                                </div>
                                                @else
                                                <div
                                                    class="object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full">
                                                </div>
                                                @endif
                                                <p>{{ $user_data->nickname }}</p>
                                            </div>
                                            {{-- ユーザーid --}}
                                            <div class="ml-16 lg:ml-0 lg:col-span-3">
                                                <p class="text-gray-500">
                                                    {{ $user_data->username }}
                                                </p>
                                            </div>
                                            {{-- メールアドレス --}}
                                            <div class="mt-3 lg:col-span-3 lg:mt-0">
                                                <p class="text-gray-500">
                                                    {{ $user_data->email }}
                                                </p>
                                            </div>

                                            <!-- 三点リーダー（モーダル） -->
                                            <div class="flex items-end justify-end col-span-3">
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
                                                                let user_id = {{ $user_data->id }};
                                                                if (confirm('本当に削除しますか？')) { @this.call('deleteUser', user_id) }">
                                                                ユーザーを削除
                                                            </button>
                                                            <button type="button"
                                                                class="block w-full p-2 text-left hover:bg-slate-100"
                                                                onclick="
                                                                let user_id = {{ $user_data->id }};
                                                                if (confirm('本当に利用停止解除にしますか？')) { @this.call('liftSuspendUser', user_id) }">
                                                                ユーザーを利用停止解除
                                                            </button>
                                                        </div>
                                                    </x-slot>
                                                </x-dropdown>
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

    {{-- ユーザー の ペジネーション --}}
    <div class="flex justify-center" x-cloak x-show="user">
        {{ $all_not_suspended_users_data_paginated->withQueryString()->links() }}
    </div>

    {{-- 利用停止中ユーザー の ペジネーション --}}
    <div class="flex justify-center" x-cloak x-show="suspension_user">
        {{ $all_suspended_users_data_paginated->withQueryString()->links() }}
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
    </script>

</div>