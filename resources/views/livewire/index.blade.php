<div>
    <x-slot name="header">
        <div class="grid items-center grid-cols-2">
            {{-- 左側 --}}
            <h2 class="font-semibold leading-tight text-gray-800">
                参加グループ一覧
            </h2>

            {{-- 右側 --}}
            <div class="flex justify-end">
                <div class="flex-wrap gap-4 sm:flex sm:gap-10 sm:flex-nowrap">
                    <div class="mb-5 sm:mb-0 sm:w-auto">
                        <a class="font-semibold leading-tight text-gray-800 " href="{{ route('group_create') }}">
                            グループをつくる
                        </a>
                    </div>
                    <div class="sm:w-auto">
                        <a class="font-semibold leading-tight text-gray-800" href="{{ route('group_join.index') }}">
                            グループに参加する
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-slot>

    <div class="px-6 pt-12 mx-auto max-w-7xl lg:px-8">
        <input type="text" wire:model.debounce.100ms="search" placeholder="グループ名か紹介文のワードで検索"
            class="w-64 text-sm rounded-xl sm:w-96">
    </div>

    <div class="py-12"> <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">

                @foreach ($my_groups_data_paginated as $group_data)
                <section class="w-full text-xs text-gray-600 body-font">
                    <div class="px-5 mx-auto">
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
                                                    class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                    <img class="object-fill w-10 h-10 rounded-full"
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
                                                    メンバー：{{ $group_data->user_roles_count }}人
                                                </p>
                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="md:ml-2">
                                            <p class="mb-3 leading-relaxed break-all">
                                                {!! nl2br(e($group_data->introduction)) !!}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- ボタン --}}
                                    <div class="px-10 pt-10 text-center">
                                        <button
                                            class="w-4/5 px-10 py-3 text-xs font-bold text-white bg-indigo-400 border-0 sm:text-sm lg:w-1/3 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                            onclick="location.href='{{ route('group.index', ['group_id' => $group_data['id']]) }}' ">入室</button>

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
        {{ $my_groups_data_paginated->links() }}
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
