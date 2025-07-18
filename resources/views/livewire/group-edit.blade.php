<div wire:init="$refresh">
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            グループ編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl text-sm sm:px-6 lg:px-8">
            <div class="grid overflow-hidden gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">

                <div class="h-8">
                    <div x-data="{ showMessage: false, message: '' }"
                        @flash-message.window="showMessage = true; message = $event.detail.message; setTimeout(() => showMessage = false, 4000);">
                        <div x-show="showMessage" x-cloak
                            class="p-2 mx-auto w-1/2 font-bold text-center text-white bg-blue-300 rounded-2xl">
                            <span x-text="message"></span>
                        </div>
                    </div>
                </div>

                {{-- グループ情報--}}
                @can('manager', $group_data)
                <section class="text-gray-600 body-font">
                    <div class="container px-5 mx-auto">
                        <div class="-m-4">
                            <form class="p-4" wire:submit.prevent="updateGroupInfo">
                                <div
                                    class="overflow-hidden relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md">
                                    <div class="grid sm:grid-cols-5">
                                        {{-- 左側 --}}
                                        <div class="sm:col-span-3">
                                            {{-- photo --}}
                                            <div class="flex content-center max-w-xs">
                                                @if($group_image_preview?->isPreviewable())
                                                <div class="object-cover mr-3 w-14 h-14 bg-center rounded-full">
                                                    <img class="object-fill w-14 h-14 rounded-full"
                                                        src="{{ $group_image_preview->temporaryUrl() }}">
                                                </div>
                                                @elseif ($group_image_delete_flag)
                                                <div
                                                    class="object-cover mr-3 w-14 h-14 bg-blue-200 bg-center rounded-full">
                                                </div>
                                                @elseif($group_data->group_photo_path)
                                                <div class="object-cover mr-3 w-14 h-14 bg-center rounded-full">
                                                    <img class="object-fill w-14 h-14 rounded-full"
                                                        src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                                                </div>
                                                @else
                                                <div
                                                    class="object-cover mr-3 w-14 h-14 bg-blue-200 bg-center rounded-full">
                                                </div>
                                                @endif
                                                {{-- end_photo --}}
                                            </div>

                                            {{-- 画像設定ボタン --}}

                                            @error('group_image_preview')
                                            <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                            @enderror

                                            <div class="flex gap-4 items-center mt-3 leading-none y-4">
                                                <div>
                                                    <label for="group_image_preview"
                                                        class="px-6 py-1 text-xs font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50">画像を選択</label>
                                                    <input id="group_image_preview" class="hidden" type="file"
                                                        wire:model.defer="group_image_preview"></input>
                                                </div>
                                                <div>
                                                    <button wire:click="deleteGroupImage" type="button"
                                                        class="px-6 py-1 text-xs font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50">画像を削除</button>
                                                </div>
                                            </div>

                                            {{-- グループ情報入力 --}}
                                            <div class="mt-5">

                                                @error('group_data.name')
                                                <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                @enderror

                                                <label for="group_name" class="block">グループ名<span
                                                        class="required">*</span></label>
                                                <input wire:model.defer="group_data.name" id="group_name" type="text"
                                                    class="w-full rounded-lg">
                                            </div>

                                            <div class="mt-5">

                                                @error('group_data.introduction')
                                                <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                @enderror

                                                <label for="introduction" class="block">グループ紹介文<span
                                                        class="required">*</span></label>
                                                <textarea wire:model.defer='group_data.introduction' id="introduction"
                                                    type="text" class="w-full rounded-lg" rows="6"></textarea>
                                            </div>

                                        </div>
                                        {{-- 右側 --}}
                                        {{-- ボタン --}}
                                        <div
                                            class="flex justify-center items-center pt-10 sm:col-span-2 sm:justify-end sm:pt-0">
                                            <button
                                                class="px-14 py-3 font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
                                                type="submit">更新する</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
                @endcan

                {{-- メンバー編集 --}}
                <section class="text-gray-600 body-font">
                    <div class="container px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="overflow-hidden relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md">
                                    <div class="items-center sm:grid sm:grid-cols-5">
                                        {{-- 左側 --}}
                                        <div class="text-center sm:text-left sm:col-span-3">
                                            <p>メンバー編集</p>
                                        </div>
                                        {{-- 右側 --}}
                                        {{-- ボタン --}}
                                        <div
                                            class="flex justify-center items-center pt-10 sm:justify-end sm:col-span-2 sm:pt-0">
                                            <button
                                                class="px-14 py-3 font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
                                                onclick="location.href='{{ route('group.member_edit', ['group_id' => $group_data->id]) }}' ">編集する</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- グループの自由参加 --}}
                @can('manager', $group_data)
                <section class="text-gray-600 body-font">
                    <div class="container px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="overflow-hidden relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md">
                                    <div class="flex justify-between items-center">
                                        {{-- 左側 --}}
                                        <div>
                                            <p>グループの自由参加</p>
                                        </div>
                                        {{-- 右側 --}}
                                        {{-- ボタン --}}
                                        @livewire('toggle-join-free-enable-button', ['group_data' => $group_data])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @endcan

                {{-- グループに招待する --}}

                <x-flash-message status="error" timeout="0"/>

                <section class="text-gray-600 body-font">
                    <div class="container px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="overflow-hidden relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md">
                                    <form wire:submit.prevent="sendInviteToGroupMail"
                                        class="items-center sm:grid sm:grid-cols-5">
                                        {{-- 左側 --}}

                                        <div class="flex justify-center sm:justify-start sm:text-left sm:col-span-3">
                                            <div>
                                                @error('email')
                                                <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                @enderror

                                                <label for="email" class="block">グループに招待する<span
                                                        class="required">*</span></label>
                                                <input id="email" type="text" name="email" wire:model.defer="email"
                                                    class="rounded-lg" placeholder="Eメールアドレス" size="30">
                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        {{-- ボタン --}}
                                        <div
                                            class="flex justify-center items-center pt-10 sm:justify-end sm:col-span-2 sm:pt-0">
                                            <button
                                                class="px-14 py-3 font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
                                                type="submit">招待する</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- TODO: グループ内投げ銭許可スイッチ機能 （この機能自体は完成しているが、クレカ・投げ銭機能ができてからコメントアウトを外す） -->
                {{-- グループ内投げ銭 --}}
                {{-- @can('manager', $group_data)
                <section class="text-gray-600 body-font">
                    <div class="container px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="overflow-hidden relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md">
                                    <div class="flex justify-between items-center"> --}}
                                        {{-- 左側 --}}
                                        {{-- <div>
                                            <p>グループ内投げ銭 <br>
                                                （100円 / 回）</p>
                                        </div> --}}
                                        {{-- 右側 --}}
                                        {{-- ボタン --}}
                                        {{-- @livewire('toggle-tip-enable-button', ['groupId' => $group_data['id']])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @endcan --}}

                {{-- グループを削除 --}}
                @can('manager', $group_data)
                <section class="text-gray-600 body-font">
                    <div class="container px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="overflow-hidden relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md">
                                    <div class="items-center sm:grid sm:grid-cols-5">
                                        {{-- 左側 --}}
                                        <div class="text-center sm:col-span-3 sm:text-left">
                                            <p>グループを削除</p>
                                        </div>
                                        {{-- 右側 --}}
                                        {{-- ボタン --}}
                                        <div
                                            class="flex justify-center items-center pt-10 sm:justify-end sm:col-span-2 sm:pt-0">
                                            <button
                                                class="px-8 py-3 font-bold text-red-700 rounded-2xl border border-red-700 focus:outline-none hover:bg-red-100"
                                                onclick="Livewire.emit('showModal')">グループを削除</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @endcan
            </div>

            {{-- グループ削除確認モーダル --}}
            @livewire('delete-group-form', ['group_data' => $group_data])
        </div>
    </div>
</div>
