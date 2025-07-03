<div x-data="{
    member: true,
    block_member: false,
}">
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            メンバー一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6">
            <div class="grid overflow-hidden gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">

                <section class="text-gray-600 body-font"
                    x-data="{modal_leave_group: false, currentUserId: null, actionUrl: ''}">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="grid gap-10 px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md sm:gap-7">
                                    {{-- メンバー / ブロック中のメンバー --}}
                                    <div class="border-b border-gray-400">
                                        <div class="flex text-xs font-bold sm:text-base lg:w-1/2">
                                            <button
                                                class="w-1/2 text-center rounded-t-xl transition duration-700 ease-in-out hover:bg-blue-100"
                                                type="button" x-on:click="member = true; block_member= false"
                                                x-bind:class="member ? 'border-b-4 border-blue-300' :'' ">
                                                <p>メンバー</p>
                                            </button>
                                            <button
                                                class="w-1/2 text-center rounded-t-xl transition duration-700 ease-in-out hover:bg-blue-100"
                                                type="button" x-on:click="member = false; block_member= true"
                                                x-bind:class="block_member ? 'border-b-4 border-blue-300' :'' ">
                                                <p>ブロック中のメンバー</p>
                                            </button>
                                        </div>
                                    </div>
                                    {{-- メンバー を選択した 場合 --}}
                                    @foreach ($all_not_blocked_users_data_paginated as $user_data)
                                    {{-- １人分のまとまり --}}
                                    <div class="grid gap-10 text-xs lg:gap-7" x-cloak x-show="member"
                                        wire:key='{{ "not_block". $user_data->id }}'>
                                        <div class="items-center lg:grid lg:grid-cols-7">
                                            {{-- プロフィール画像 ・ ニックネーム --}}
                                            <div class="flex items-center lg:col-span-2">
                                                @if($user_data->profile_photo_path)
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $group_data->id ,'user_id' => $user_data->id]) }}' "
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full">
                                                    <img class="object-cover w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $group_data->id ,'user_id' => $user_data->id]) }}' "
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full">
                                                    <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                </button>
                                                @endif
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $group_data->id ,'user_id' => $user_data->id]) }}' ">
                                                    {{ $user_data->nickname }}
                                                </button>
                                            </div>
                                            {{-- ユーザーid --}}
                                            <div class="ml-16 lg:ml-0 lg:col-span-2">
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $group_data->id ,'user_id' => $user_data->id]) }}' "
                                                    class="text-gray-500">
                                                    {{ $user_data->username }}
                                                </button>
                                            </div>
                                            {{-- 投稿数 ・ 権限 ・ 三点リーダー（モーダル） --}}
                                            <div
                                                class="grid grid-cols-3 items-center text-center lg:text-left lg:col-span-3">
                                                {{-- 投稿数 --}}
                                                <div class="mt-3 lg:mt-0">
                                                    <p>{{ $user_data->memo_count }}<span class="ml-3">投稿</span></p>
                                                </div>
                                                {{-- 権限 --}}

                                                @php
                                                $role = $user_data->groupRoles->first()->pivot->role;
                                                @endphp

                                                @if($is_manager)
                                                {{-- 自分が管理者の場合 --}}
                                                <div class="mt-3 lg:mt-0">
                                                    @if ($role == 10)
                                                    <p class="self-end text-xs">管理者</p>
                                                    @else
                                                    <select
                                                        wire:change="checkUpdateRole({{ $user_data->id }}, $event.target.value)"
                                                        class="pl-0 text-xs bg-transparent border-none">
                                                        <option value="10" {{ ($role==10) ? 'selected' : '' }}>
                                                            管理者
                                                        </option>
                                                        <option value="50" {{ ($role==50) ? 'selected' : '' }}>
                                                            サブ管理者
                                                        </option>
                                                        <option value="100" {{ ($role==100) ? 'selected' : '' }}>
                                                            メンバー
                                                        </option>
                                                    </select>
                                                    @endif
                                                </div>
                                                @else
                                                {{-- 自分が管理者でない場合 --}}
                                                {{-- （管理者以外は権限を変更できない） --}}
                                                    @if ($role == 10)
                                                    <p class="self-end text-xs">管理者</p>
                                                    @elseif ($role == 50)
                                                    サブ管理者
                                                    @elseif ($role == 100)
                                                    メンバー
                                                    @endif
                                                @endif
                                                <!-- 三点リーダー（モーダル） -->
                                                @if(auth()->id() != $user_data->id)
                                                <div class="flex justify-end items-end">
                                                    <x-dropdown align="right" width="48">
                                                        <x-slot name="trigger">
                                                            <button
                                                                class="flex text-sm border-2 border-transparent transition focus:outline-none">
                                                                <i class="text-lg fas fa-ellipsis-v"></i>
                                                            </button>
                                                        </x-slot>

                                                        <!-- モーダルの中身 -->
                                                        <x-slot name="content">
                                                            <div class="flex flex-col px-4 text-gray-800">
                                                                <button class="block p-2 text-left hover:bg-slate-100"
                                                                    onclick="Livewire.emit('showMemberQuitModal', {{ $user_data->id }})">退会させる</button>

                                                                <button class="block p-2 text-left hover:bg-slate-100"
                                                                    wire:click="blockMember({{ $user_data->id }})">ブロックする</button>
                                                            </div>
                                                        </x-slot>
                                                    </x-dropdown>
                                                </div>
                                                @endif

                                            </div>

                                        </div>
                                    </div>
                                    @endforeach

                                    {{-- ブロック中のメンバー を選択した場合 --}}
                                    @foreach ($all_blocked_users_data_paginated as $user_data)
                                    <div class="grid gap-10 lg:gap-7" x-cloak x-show="block_member"
                                        wire:key='{{ "block". $user_data->id }}'>
                                        {{-- １人分のまとまり --}}
                                        <div class="items-center text-xs lg:grid lg:grid-cols-7">
                                            {{-- プロフィール画像 ・ ニックネーム --}}
                                            <div class="flex items-center lg:col-span-2">
                                                @if($user_data->profile_photo_path)
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $group_data->id ,'user_id' => $user_data->id]) }}' "
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full">
                                                    <img class="object-cover w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                                                </button>
                                                @else
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $group_data->id ,'user_id' => $user_data->id]) }}' "
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full">
                                                    <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                </button>
                                                @endif
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $group_data->id ,'user_id' => $user_data->id]) }}' ">
                                                    {{ $user_data->nickname }}
                                                </button>
                                            </div>
                                            {{-- ユーザーid --}}
                                            <div class="ml-16 lg:mt-0 lg:ml-0 lg:col-span-2">
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $group_data->id ,'user_id' => $user_data->id]) }}' "
                                                    class="text-gray-500">
                                                    {{ $user_data->username }}
                                                </button>
                                            </div>
                                            {{-- 投稿数 ・ 権限 ・ 三点リーダー（モーダル） --}}
                                            <div
                                                class="grid grid-cols-3 items-center text-center lg:text-left lg:col-span-3">
                                                {{-- 投稿数 --}}
                                                <div class="mt-3 lg:mt-0">
                                                    <p>{{ $user_data->memo_count }}<span class="ml-3">投稿</span></p>
                                                </div>
                                                {{-- 権限 --}}
                                                <div class="mt-3 lg:mt-0">
                                                    <p class="text-xs lg:text-base">
                                                        @php
                                                        $role = $user_data->groupRoles->first()->pivot->role;
                                                        @endphp

                                                    @if($is_manager)
                                                    {{-- 自分が管理者の場合 --}}
                                                    <div class="mt-3 lg:mt-0">
                                                        <select
                                                            wire:change="updateRole({{ $user_data->id }}, $event.target.value)"
                                                            class="pl-0 text-xs bg-transparent border-none">
                                                            <option value="50" {{ ($role==50) ? 'selected' : '' }}>サブ管理者
                                                            </option>
                                                            <option value="100" {{ ($role==100) ? 'selected' : '' }}>
                                                                メンバー
                                                            </option>
                                                        </select>
                                                    </div>
                                                    @else
                                                    {{-- 自分が管理者でない場合 --}}
                                                    {{-- （管理者以外は権限を変更できない） --}}
                                                    @if ($role == 10)
                                                    <p class="text-xs">管理者</p>
                                                    @elseif ($role == 50)
                                                    サブ管理者
                                                    @elseif ($role == 100)
                                                    メンバー
                                                    @endif
                                                    @endif
                                                    </p>
                                                </div>
                                                <!-- 三点リーダー（モーダル） -->
                                                @if(auth()->id() != $user_data->id)
                                                <div class="flex justify-end items-end">
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
                                                                <button class="block p-2 text-left hover:bg-slate-100"
                                                                    onclick="Livewire.emit('showMemberQuitModal', {{ $user_data->id }})">退会させる</button>

                                                                <button class="block p-2 text-left hover:bg-slate-100"
                                                                    wire:click="liftBlockMember({{ $user_data->id }})">ブロック解除する</button>
                                                            </div>
                                                        </x-slot>
                                                    </x-dropdown>
                                                </div>
                                                @endif

                                            </div>

                                        </div>
                                    </div>
                                    @endforeach

                                </div>


                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>

        {{-- 退会確認モーダル --}}
        @livewire('quit-group-form-of-member-edit-page', ['group_data' => $group_data])

    </div>


    {{-- メンバー の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="member">
        {{ $all_not_blocked_users_data_paginated->withQueryString()->links() }}
    </div>

    {{-- ブロック中メンバー の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="block_member">
        {{ $all_blocked_users_data_paginated->withQueryString()->links() }}
    </div>

    {{-- 管理者を変更しようとした場合に確認メッセージを出す（イベントを発行して、ビュー側で確認メッセージを出す） --}}
    <script>
        Livewire.on('checkUpdateRole', function (userId, role) {
            if (confirm('本当に実行しますか？\n' +
                        'この操作を実行した場合、あなたはこのグループのサブ管理者となり、\n' +
                        '管理者権限でのすべての操作ができなくなります。\n' +
                        'この操作は取り消すことができません。')) {
                Livewire.emit('updateRole', userId, role);
            }else {
                // 画面をリフレッシュする
                location.reload();
            }
        });
    </script>
</div>
