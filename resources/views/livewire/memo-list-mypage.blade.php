<div x-data="{
    my_memo: @entangle('show_my_memos'),
    good_memo: @entangle('show_good_memos'),
    later_read_memo: @entangle('show_later_read_memos')
}">
    <x-slot name="header">
        <div class="flex">
            <h2 class="font-semibold leading-tight text-gray-800">
                マイページ
            </h2>
            <p class="text-xs ml-14 sm:ml-28">{{ $count_all_my_memos_data }}<span class="ml-3 text-xs">投稿</span></p>
        </div>
    </x-slot>

    <div class="flex flex-col-reverse gap-8 px-6 pt-12 mx-auto lg:flex-row max-w-7xl lg:px-8">
        <div>
            <input type="text" wire:model.debounce.100ms="search" placeholder="タイトルかメモ概要のワードで検索"
                class="w-64 text-sm rounded-xl sm:w-96">
        </div>

        <select wire:change="setGroupId($event.target.value)" class="max-w-xs text-sm rounded-xl">
            <option value="">グループで絞り込み</option>
            @foreach($user_groups as $group)
            <option value="{{ $group->id }}" {{ $group_id==$group->id ? 'selected' : '' }}>
                {{ $group->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="py-12 lg:grid-cols-12 lg:grid">
        {{-- ラベル一覧（左） --}}
        <div class="absolute z-20 col-span-2 lg:block lg:static">
            <input type="checkbox" id="drawer-toggle" class="sr-only peer" checked>
            <label for="drawer-toggle" class="left-0 inline-block p-2 bg-indigo-500 rounded-lg lg:hidden top-40 ">
                <div class="w-6 h-1 mb-3 bg-white rounded-lg"></div>
                <div class="w-6 h-1 bg-white rounded-lg"></div>
            </label>
            <div class="hidden h-full bg-white shadow-lg rounded-r-2xl peer-checked:block lg:block">
                <div class="z-20 px-1 py-2 overflow-auto lg:static max-h-96 label-list-container">
                    {{-- ラベル表示 --}}
                    <div class="lg:col-span-2">

                        @livewire('web-book-label')

                        @livewire('label-list-mypage')

                        {{-- ラベル編集 --}}
                        <div>
                            @if($group_id)
                            @can('manager', $group_data)
                            <div>
                                <button onclick="Livewire.emit('showLabelEditModal')"
                                    class="flex items-center w-full gap-4 p-2 hover:bg-slate-100"><i
                                        class="lg:text-xl fa-solid fa-pencil fa-fw"></i>
                                    <p class="text-xs lg:text-sm">ラベルを編集</p>
                                </button>
                            </div>
                            @endcan
                            @endif
                            {{-- ラベル編集モーダル --}}


                            {{-- ラベル名入力 --}}
                            @livewire('label-editor-mypage')


                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- メインコンテンツ（中央） --}}
        <div class="w-full mx-auto text-xs max-w-7xl sm:px-6 lg:px-8 lg:col-span-8">
            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
                {{-- 自分が作成したメモ / いいねしたメモ / あとでよむにしたメモ 選択--}}
                <div class="mx-3 mb-10 border-b border-gray-400">
                    <div class="flex text-xs font-bold lg:text-sm xl:w-1/2">
                        <button
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            type="button" x-on:click="my_memo = true; good_memo=false; later_read_memo=false"
                            x-bind:class="my_memo ? 'border-b-4 border-blue-300' :'' ">
                            <p>自分が作成したメモ</p>
                        </button>
                        <button
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            type="button" x-on:click="my_memo = false; good_memo=true; later_read_memo=false"
                            x-bind:class="good_memo ? 'border-b-4 border-blue-300' :'' ">
                            <p>いいねしたメモ</p>
                        </button>
                        <button
                            class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                            type="button" x-on:click="my_memo = false; good_memo=false; later_read_memo=true"
                            x-bind:class="later_read_memo ? 'border-b-4 border-blue-300' :'' ">
                            <p>あとでよむしたメモ</p>
                        </button>
                    </div>
                </div>
                {{-- 自分が作成したメモ　を　選択しているとき --}}
                <div class="grid gap-10" x-cloak x-show="my_memo">
                    @foreach ($all_my_memos_data_paginated as $memo_data)
                    @if ($memo_data->type == 0)
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4">
                                <div class="p-4">
                                    <div
                                        class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md lg:px-8 rounded-2xl">
                                        <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                            {{-- 左側 --}}
                                            <div class="xl:col-span-3">
                                                <div class="flex items-center content-center">
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
                                                        class="text-sm font-bold text-left text-gray-700 break-all title-font"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'web'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3">
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-created1-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-created1-'.microtime(true)))
                                                    </div>
                                                </div>
                                                {{-- タグ --}}
                                                @if (!$memo_data->labels->isEmpty())
                                                <div class="mt-8">
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
                                                <div class="grid gap-10 px-10 pt-10 lg:px-0 lg:grid-cols-2 lg:gap-5">
                                                    @if ($memo_data['user_id'] === Auth::id() )
                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 rounded-2xl lg:px-1 focus:outline-none hover:bg-indigo-500"
                                                        onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'web'] ) }}' ">編集する</button>
                                                    @else
                                                    <div class=""></div>
                                                    @endif

                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                        onclick="window.open('{{ $memo_data->web_type_feature->url }}') ">リンクを開く</button>
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
                        <div class="px-5 mx-auto ">
                            <div class="-m-4 ">
                                <div class="p-4">
                                    <div
                                        class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md lg:px-8 rounded-2xl">
                                        <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                            {{-- 左側 --}}
                                            <div class="xl:col-span-3">
                                                <div class="flex items-center content-center">
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
                                                        class="text-sm font-bold text-left text-gray-700 break-all title-font"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3">
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-created2-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-created2-'.microtime(true)))
                                                    </div>
                                                </div>
                                                {{-- タグ --}}
                                                @if (!$memo_data->labels->isEmpty())
                                                <div class="mt-8">
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
                                                <div class="grid gap-10 px-10 mt-10 lg:px-0 lg:gap-5 lg:grid-cols-2">
                                                    @if ($memo_data['user_id'] === Auth::id() )
                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                        onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'book'] ) }}' ">編集する</button>
                                                    @else
                                                    <div class=""></div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- 右側 --}}
                                            <div class="grid grid-cols-5">
                                                <div class="col-span-5">
                                                    <div class="max-w-xs m-auto">
                                                        <div class="hidden text-right xl:block">
                                                            <i class="text-xl fas fa-book-open"></i>
                                                        </div>

                                                        <div class="flex justify-center">
                                                            @if($memo_data->book_type_feature && $memo_data->book_type_feature->book_photo_path)
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

                {{-- いいねしたメモ　を　選択しているとき --}}
                <div class="grid gap-10" x-cloak x-show="good_memo">
                    @foreach ($all_good_memos_data_paginated as $memo_data)
                    @if ($memo_data->type == 0)
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4 ">
                                <div class="p-4">
                                    <div
                                        class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md lg:px-8 rounded-2xl">
                                        <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                            {{-- 左側 --}}
                                            <div class="xl:col-span-3">
                                                <div class="flex items-center content-center">
                                                    {{-- photo --}}
                                                    @if($memo_data->user->profile_photo_path)
                                                    <button
                                                        onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img class="object-fill w-10 h-10 rounded-full"
                                                            src="{{ asset('storage/'. $memo_data->user->profile_photo_path) }}" />
                                                    </button>
                                                    @else
                                                    <button
                                                        onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                    </button>
                                                    @endif
                                                    {{-- メモ作成者情報 --}}
                                                    <div>
                                                        <div class="mb-1 lg:mb-0">
                                                            <button
                                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                                class="block ml-3 text-black">
                                                                {{ $memo_data->user->nickname }}
                                                            </button>
                                                            <button
                                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                                class="ml-5 text-gray-500">
                                                                {{ $memo_data->user->username }}
                                                            </button>
                                                        </div>
                                                        <div class="inline mt-1 ml-5 text-gray-500">
                                                            <i class="fa-regular fa-clock"></i>
                                                            <span>
                                                                {{ $memo_data['created_at']->format('Y-m-d') }}
                                                            </span>
                                                        </div>
                                                        <div class="inline mt-1 ml-5 text-gray-500">
                                                            <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                            <span>
                                                                {{ $memo_data['updated_at']->format('Y-m-d')}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- メモタイトル --}}
                                                <div class="mt-5 ml-3 leading-none y-4">
                                                    <button
                                                        class="font-bold text-left text-gray-700 break-all sm:text-sm title-font"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3">
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-liked3-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-liked3-'.microtime(true)))
                                                    </div>
                                                </div>
                                                {{-- タグ --}}
                                                @if (!$memo_data->labels->isEmpty())
                                                <div class="mt-8">
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
                                                <div class="grid gap-10 px-10 pt-10 lg:grid-cols-2 lg:gap-5 lg:px-0">
                                                    @if ($memo_data['user_id'] === Auth::id() )
                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                        onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'web'] ) }}' ">編集する</button>
                                                    @else
                                                    <div class=""></div>
                                                    @endif

                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500">リンクを開く</button>
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
                            <div class="-m-4 ">
                                <div class="p-4">
                                    <div
                                        class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                                        <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                            {{-- 左側 --}}
                                            <div class="xl:col-span-3">
                                                <div class="flex items-center content-center">
                                                    {{-- photo --}}
                                                    @if($memo_data->user->profile_photo_path)
                                                    <button
                                                        onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img class="object-fill w-10 h-10 rounded-full"
                                                            src="{{ asset('storage/'. $memo_data->user->profile_photo_path) }}" />
                                                    </button>
                                                    @else
                                                    <button
                                                        onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                    </button>
                                                    @endif
                                                    {{-- メモ作成者情報 --}}
                                                    <div>
                                                        <div class="mb-1 lg:mb-0">
                                                            <button
                                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                                class="block ml-3 text-black">
                                                                {{ $memo_data->user->nickname }}
                                                            </button>
                                                            <button
                                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                                class="ml-5 text-gray-500">
                                                                {{ $memo_data->user->username }}
                                                            </button>
                                                        </div>
                                                        <div class="inline mt-1 ml-5 text-gray-500">
                                                            <i class="fa-regular fa-clock"></i>
                                                            <span>
                                                                {{ $memo_data['created_at']->format('Y-m-d') }}
                                                            </span>
                                                        </div>
                                                        <div class="inline mt-1 ml-5 text-gray-500">
                                                            <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                            <span>
                                                                {{ $memo_data['updated_at']->format('Y-m-d')}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- メモタイトル --}}
                                                <div class="mt-5 ml-3 leading-none y-4">
                                                    <button
                                                        class="font-bold text-left text-gray-700 break-all sm:text-sm title-font"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3">
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-liked4-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-liked4-'.microtime(true)))
                                                    </div>
                                                </div>
                                                {{-- タグ --}}
                                                @if (!$memo_data->labels->isEmpty())
                                                <div class="mt-8">
                                                    @foreach ($memo_data->labels as $label)
                                                    <div
                                                        class="inline-block px-3 py-1 m-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                                                        {{ $label->name }}</div>
                                                    @endforeach
                                                </div>
                                                @endif


                                            </div>
                                            {{-- 真ん中 --}}
                                            <div class="flex flex-col justify-between xl:col-span-3 xl:ml-2">
                                                {{-- shortMemo --}}
                                                <div class="flex">
                                                    <p class="mb-3 leading-relaxed break-all">
                                                        {!! nl2br(e($memo_data['shortMemo'])) !!}
                                                    </p>
                                                </div>

                                                {{-- ボタン --}}
                                                <div class="grid gap-10 px-10 mt-6 lg:grid-cols-2 lg:gap-5 lg:px-0">
                                                    @if ($memo_data['user_id'] === Auth::id() )
                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                        onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'book'] ) }}' ">編集する</button>
                                                    @else
                                                    <div class=""></div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- 右側 --}}
                                            <div class="grid grid-cols-5">
                                                <div class="col-span-5">
                                                    <div class="max-w-xs m-auto">
                                                        <div class="hidden text-right xl:block">
                                                            <i class="text-xl fas fa-book-open"></i>
                                                        </div>
                                                        <div class="flex justify-center">
                                                            @if($memo_data->book_type_feature && $memo_data->book_type_feature->book_photo_path)
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

                {{-- あとでよむにしたメモ　を　選択しているとき --}}
                <div class="grid gap-10" x-cloak x-show="later_read_memo">
                    @foreach ($all_later_read_memos_data_paginated as $memo_data)
                    @if ($memo_data->type == 0)
                    <section class="text-gray-600 body-font">
                        <div class="px-5 mx-auto">
                            <div class="-m-4 ">
                                <div class="p-4">
                                    <div
                                        class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md lg:px-8 rounded-2xl">
                                        <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                            {{-- 左側 --}}
                                            <div class="xl:col-span-3">
                                                <div class="flex items-center content-center">
                                                    {{-- photo --}}
                                                    @if($memo_data->user->profile_photo_path)
                                                    <button
                                                        onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                        class="object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img class="object-fill w-10 h-10 rounded-full"
                                                            src="{{ asset('storage/'. $memo_data->user->profile_photo_path) }}" />
                                                    </button>
                                                    @else
                                                    <button
                                                        onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                        class="object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                    </button>
                                                    @endif
                                                    {{-- メモ作成者情報 --}}
                                                    <div>
                                                        <div class="mb-1 lg:mb-0">
                                                            <button
                                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                                class="block ml-3 text-black">
                                                                {{ $memo_data->user->nickname }}
                                                            </button>
                                                            <button
                                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                                class="ml-5 text-gray-500">
                                                                {{ $memo_data->user->username }}
                                                            </button>
                                                        </div>
                                                        <div class="inline mt-1 ml-5 text-gray-500">
                                                            <i class="fa-regular fa-clock"></i>
                                                            <span>
                                                                {{ $memo_data['created_at']->format('Y-m-d') }}
                                                            </span>
                                                        </div>
                                                        <div class="inline mt-1 ml-5 text-gray-500">
                                                            <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                            <span>
                                                                {{ $memo_data['updated_at']->format('Y-m-d')}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- メモタイトル --}}
                                                <div class="mt-5 ml-3 leading-none y-4">
                                                    <button
                                                        class="text-sm font-bold text-left text-gray-700 break-all title-font"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3">
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-later-read5-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-later-read5-'.microtime(true)))
                                                    </div>
                                                </div>
                                                {{-- タグ --}}
                                                @if (!$memo_data->labels->isEmpty())
                                                <div class="mt-8">
                                                    @foreach ($memo_data->labels as $label)
                                                    <div
                                                        class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
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
                                                <div class="grid gap-10 px-10 pt-10 lg:px-0 lg:grid-cols-2 lg:gap-5">
                                                    @if ($memo_data['user_id'] === Auth::id() )
                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                        onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'web'] ) }}' ">編集する</button>
                                                    @else
                                                    <div class=""></div>
                                                    @endif

                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500">リンクを開く</button>
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
                            <div class="-m-4 ">
                                <div class="p-4">
                                    <div
                                        class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md lg:px-8 rounded-2xl">
                                        <div class="grid gap-10 xl:grid-cols-7 xl:gap-0">
                                            {{-- 左側 --}}
                                            <div class="xl:col-span-3">
                                                <div class="flex items-center content-center">
                                                    {{-- photo --}}
                                                    @if($memo_data->user->profile_photo_path)
                                                    <button
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img class="object-fill w-10 h-10 rounded-full"
                                                            src="{{ asset('storage/'. $memo_data->user->profile_photo_path) }}" />
                                                    </button>
                                                    @else
                                                    <button
                                                        onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 bg-center rounded-full">
                                                        <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                    </button>
                                                    @endif
                                                    {{-- メモ作成者情報 --}}
                                                    <div>
                                                        <div class="mb-1 lg:mb-0">
                                                            <button
                                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                                class="block ml-3 text-black">
                                                                {{ $memo_data->user->nickname }}
                                                            </button>
                                                            <button
                                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data->user->id]) }}' "
                                                                class="ml-5 text-gray-500">
                                                                {{ $memo_data->user->username }}
                                                            </button>
                                                        </div>
                                                        <div class="inline mt-1 ml-5 text-gray-500">
                                                            <i class="fa-regular fa-clock"></i>
                                                            <span>
                                                                {{ $memo_data['created_at']->format('Y-m-d') }}
                                                            </span>
                                                        </div>
                                                        <div class="inline mt-1 ml-5 text-gray-500">
                                                            <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                            <span>
                                                                {{ $memo_data['updated_at']->format('Y-m-d')}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- メモタイトル --}}
                                                <div class="mt-5 ml-3 leading-none y-4">
                                                    <button
                                                        class="font-bold text-left text-gray-700 break-all sm:text-sm title-font"
                                                        onclick="location.href='{{ route('group.memo_show', ['memo_id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                                                        $memo_data['title'] }}
                                                    </button>
                                                </div>
                                                {{-- 『いいね』 『あとでよむ』 --}}
                                                <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3">
                                                    <div class="w-20">
                                                        @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-later-read6-'.microtime(true)))
                                                    </div>
                                                    <div class="w-20">
                                                        @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-later-read6-'.microtime(true)))
                                                    </div>
                                                </div>
                                                {{-- タグ --}}
                                                @if (!$memo_data->labels->isEmpty())
                                                <div class="mt-8">
                                                    @foreach ($memo_data->labels as $label)
                                                    <div
                                                        class="inline-block px-3 py-1 m-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                                                        {{ $label->name }}</div>
                                                    @endforeach
                                                </div>
                                                @endif


                                            </div>
                                            {{-- 真ん中 --}}
                                            <div class="flex flex-col justify-between xl:col-span-3 xl:ml-2">
                                                {{-- shortMemo --}}
                                                <div class="flex">
                                                    <p class="mb-3 leading-relaxed break-all">
                                                        {!! nl2br(e($memo_data['shortMemo'])) !!}
                                                    </p>
                                                </div>

                                                {{-- ボタン --}}
                                                <div class="grid gap-10 px-10 mt-6 lg:gap-5 lg:grid-cols-2 lg:px-0">
                                                    @if ($memo_data['user_id'] === Auth::id() )
                                                    <button
                                                        class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 lg:px-1 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                                        onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'book'] ) }}' ">編集する</button>
                                                    @else
                                                    <div class=""></div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- 右側 --}}
                                            <div class="grid grid-cols-5">
                                                <div class="col-span-5">
                                                    <div class="max-w-xs m-auto">
                                                        <div class="hidden text-right xl:block">
                                                            <i class="text-xl fas fa-book-open"></i>
                                                        </div>
                                                        <div class="flex justify-center">
                                                            @if($memo_data->book_type_feature && $memo_data->book_type_feature->book_photo_path)
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

            </div>
        </div>
    </div>

    {{-- 全てのメモ の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="my_memo">
        {{ $all_my_memos_data_paginated->withQueryString()->links() }}
    </div>

    {{-- いいねしたメモ の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="good_memo">
        {{ $all_good_memos_data_paginated->withQueryString()->links() }}
    </div>

    {{-- 後で読むメモ の ページネーション --}}
    <div class="flex justify-center" x-cloak x-show="later_read_memo">
        {{ $all_later_read_memos_data_paginated->withQueryString()->links() }}
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


            //ラベル一覧モーダルにカーソルがある間、その他の部分がスクロールしないようにするための処理
            document.addEventListener('DOMContentLoaded', function() {
                const labelList = document.querySelector('.label-list-container');

                labelList.addEventListener('mouseenter', function() {
                    document.body.style.overflow = 'hidden';
                });

                labelList.addEventListener('mouseleave', function() {
                    document.body.style.overflow = 'auto';
                });
            });
    </script>
    </script>

</div>
