<div>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            グループ参加
        </h2>
    </x-slot>

    <div class="px-6 pt-12 mx-auto max-w-7xl lg:px-8">
        <form wire:submit.prevent="executeSearch">
            <input type="text" wire:model.defer="search" placeholder="グループ名か紹介文のワードで検索"
                class="w-64 text-sm rounded-xl sm:w-96">
            <button class="px-3 py-2 font-bold" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    <div class="py-12">

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-center gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">

                <x-flash-message status="isNotJoinFreeEnabled" />

                @foreach ( $all_groups_data_paginated as $group_data )
                <section class="w-full text-xs text-gray-600 body-font">
                    <div class="px-5 ">
                        <div class="flex flex-wrap justify-center -m-4">
                            <div class="w-full p-4">
                                <div class="relative px-8 py-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                                    <div class="grid gap-10 sm:gap-0 sm:grid-cols-2">
                                        {{-- 左側 --}}
                                        <div>
                                            <div class="flex items-center content-center">
                                                {{-- photo --}}
                                                @if($group_data->group_photo_path)
                                                <div
                                                    class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                    <img class="object-cover w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                                                </div>
                                                @else
                                                <div
                                                    class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full">
                                                </div>
                                                @endif
                                                {{-- end_photo --}}
                                                <h1 class="self-center text-sm font-bold text-gray-700 title-font">
                                                    {{
                                                    $group_data->name }}
                                                </h1>
                                            </div>
                                            <div class="mt-2 leading-none y-4 ">
                                                <p class="items-center pt-5 ml-3 leading-none text-gray-700">
                                                    管理者：{{ $group_data->userRoles->first()->nickname }}
                                                </p>
                                                <p class="items-center pt-5 ml-3 leading-none text-gray-700">
                                                    メンバー：{{ $group_data->user_count }}人
                                                </p>
                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="">
                                            <p class="mb-3 leading-relaxed break-all">
                                                {!! nl2br(e($group_data->introduction)) !!}
                                            </p>
                                        </div>

                                    </div>

                                    {{-- ボタン --}}
                                    <div class="px-10 pt-10 text-center">
                                        @if ($group_data->isJoinFreeEnabled)
                                        <button type="button"
                                            class="w-4/5 px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:w-1/3 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                            wire:click="joinGroup({{ $group_data->id }})">参加</button>
                                        @endif

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

    <div class="flex justify-center">
        {{ $all_groups_data_paginated->links() }}
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