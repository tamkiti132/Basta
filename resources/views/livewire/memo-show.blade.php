<div x-data="{
    report_memo: @entangle('show_reports_memo'),
}">
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            メモ詳細
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl text-xs sm:px-6 lg:px-8">

            <div class="grid overflow-hidden gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">
                @if ($memo_data->type == 0)
                {{-- メモセクション --}}
                {{-- Webタイプの　場合 --}}
                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md sm:px-8">
                                    <div class="grid gap-10 lg:grid-cols-7 lg:gap-0">
                                        {{-- 左側 --}}
                                        <div class="lg:col-span-3">
                                            <div class="flex content-center items-center">
                                                {{-- photo --}}
                                                @if($memo_data->user->profile_photo_path)
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data['user_id']]) }}' "
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full">
                                                    <img class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $memo_data->user->profile_photo_path) }}" /></button>
                                                @else
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data['user_id']]) }}' "
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full">
                                                    <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                </button>
                                                @endif
                                                {{-- メモ作成者情報 --}}
                                                <div>
                                                    <div class="mb-1 lg:mb-0">
                                                        <button
                                                            onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data['user_id']]) }}' "
                                                            class="block ml-3 text-black">
                                                            {{ $memo_data->user->nickname }}
                                                        </button>
                                                        <button
                                                            onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data['user_id']]) }}' "
                                                            class="ml-5 text-gray-500">
                                                            {{ $memo_data->user->username }}
                                                        </button>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>{{ $memo_data->created_at->format('Y-m-d') }}</span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                        <span>{{ $memo_data->updated_at->format('Y-m-d')}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- メモタイトル --}}
                                            <div class="mt-5 ml-3 leading-none y-4">
                                                <h1
                                                    class="self-center text-sm font-bold text-gray-700 break-all title-font">
                                                    {{
                                                    $memo_data->title }}
                                                </h1>
                                            </div>
                                            {{-- 『いいね』 『あとでよむ』 --}}
                                            <div class="grid grid-cols-2 gap-10 mt-5 ml-3 w-20">
                                                <div class="w-20">
                                                    @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-'.microtime(true)))
                                                </div>
                                                <div class="w-20">
                                                    @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-'.microtime(true)))
                                                </div>
                                            </div>
                                            {{-- タグ --}}
                                            <div class="mt-8">
                                                @foreach ($memo_data->labels as $label)
                                                <div
                                                    class="inline-block px-3 py-1 m-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                                                    {{ $label->name }}</div>
                                                @endforeach
                                            </div>


                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="flex flex-col lg:ml-3 lg:justify-between lg:col-span-3">
                                            {{-- shortMemo --}}
                                            <div class="flex">
                                                <p class="mb-3 leading-relaxed break-all">
                                                    {!! nl2br(e($memo_data->shortMemo)) !!}
                                                </p>
                                            </div>

                                            {{-- ボタン --}}
                                            <div
                                                class="grid gap-10 self-center mt-6 w-3/4 md:grid-cols-2 lg:text-right">
                                                @if ($memo_data['user_id'] === Auth::id() )
                                                <button
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 lg:px-3 focus:outline-none hover:bg-indigo-500"
                                                    onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'web'] ) }}' ">編集する</button>
                                                @else
                                                <div class=""></div>
                                                @endif

                                                <button
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 lg:px-3 focus:outline-none hover:bg-indigo-500"
                                                    onclick="window.open('{{ $memo_data->web_type_feature->url }}') ">リンクを開く</button>
                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="hidden lg:grid-cols-5 lg:grid">
                                            <div class="lg:col-span-2">
                                            </div>
                                            <div class="lg:col-span-3">
                                                <div class="text-right">
                                                    <i class="text-xl fas fa-globe"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-12 mt-4">
                                        <div class="col-span-11 rounded-lg border-2">
                                            <p class="break-all">
                                                @if ($memo_data->additionalMemo)
                                                {!! nl2br(e($memo_data->additionalMemo)) !!}
                                                @endif
                                            </p>
                                        </div>

                                        <!-- 三点リーダー（モーダル） -->
                                        <div class="flex justify-end items-end">
                                            <x-dropdown align="right" width="48">
                                                <x-slot name="trigger">
                                                    <button
                                                        class="flex border-2 border-transparent transition focus:outline-none">
                                                        <i class="text-xl fas fa-ellipsis-v"></i>
                                                    </button>
                                                </x-slot>

                                                <!-- モーダルの中身 -->
                                                <x-slot name="content">
                                                    <div class="flex flex-col px-4 text-gray-800">
                                                        <button onclick="Livewire.emit('showModalReportMemo')"
                                                            class="block p-2 w-full text-left hover:bg-slate-100">
                                                            メモを通報
                                                        </button>

                                                        @if (auth()->id() === $memo_data->user_id)
                                                            <button
                                                            onclick="if (confirm('本当に削除しますか？')) { @this.call('deleteMemo', {{ $memo_data->id }}) }"
                                                            class="block p-2 w-full text-left hover:bg-slate-100">
                                                            メモを削除
                                                            </button>
                                                        @endif

                                                    </div>
                                                </x-slot>
                                            </x-dropdown>
                                        </div>

                                    </div>
                                    {{-- メモ通報モーダルのトグルボタン --}}
                                    @can('admin-higher')
                                    @if ( $memo_data->reports_count )
                                    <div class="flex justify-center">
                                        <button x-on:click="report_memo = !report_memo"
                                            class="pt-5 text-gray-600 hover:text-black"><span
                                                x-show="!report_memo">通報内容を見る（{{$memo_data->reports_count}}件）</span>
                                            <span x-show="report_memo">通報内容を隠す（{{$memo_data->reports_count}}件）</span>
                                        </button>
                                    </div>
                                    @endif
                                    @endcan


                                </div>


                            </div>
                        </div>
                    </div>
                    {{-- メモ通報モーダル --}}
                    @livewire('report-memo', ['memo_id' => $memo_data->id])
                </section>
                @elseif ($memo_data->type == 1)
                {{-- 本タイプ　の場合 --}}
                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md sm:px-8">
                                    <div class="grid gap-10 lg:grid-cols-7 lg:gap-0">
                                        {{-- 左側 --}}
                                        <div class="lg:col-span-3">
                                            <div class="flex content-center items-center">
                                                {{-- photo --}}
                                                @if($memo_data->user->profile_photo_path)
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data['user_id']]) }}' "
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full"><img
                                                        class="object-fill w-10 h-10 rounded-full"
                                                        src="{{ asset('storage/'. $memo_data->user->profile_photo_path) }}" /></button>
                                                @else
                                                <button
                                                    onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data['user_id']]) }}' "
                                                    class="object-cover mr-3 w-10 h-10 bg-center rounded-full">
                                                    <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                </button>
                                                @endif
                                                {{-- メモ作成者情報 --}}
                                                <div>
                                                    <div class="mb-1 lg:mb-0">
                                                        <button
                                                            onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data['user_id']]) }}' "
                                                            class="block ml-3 text-black">
                                                            {{ $memo_data->user->nickname }}
                                                        </button>
                                                        <button
                                                            onclick="location.href='{{ route('group.member_show', ['group_id' => $memo_data->group_id ,'user_id' => $memo_data['user_id']]) }}' "
                                                            class="ml-5 text-gray-500">
                                                            {{ $memo_data->user->username }}
                                                        </button>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <span>{{ $memo_data->created_at->format('Y-m-d') }}</span>
                                                    </div>
                                                    <div class="inline mt-1 ml-5 text-gray-500">
                                                        <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                                                        <span>{{ $memo_data->updated_at->format('Y-m-d')}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- メモタイトル --}}
                                            <div class="mt-5 ml-3 leading-none y-4">
                                                <h1
                                                    class="self-center text-sm font-bold text-gray-700 break-all title-font">
                                                    {{
                                                    $memo_data->title }}
                                                </h1>
                                            </div>
                                            {{-- 『いいね』 『あとでよむ』 --}}
                                            <div class="grid grid-cols-2 gap-10 mt-5 ml-3 w-20">
                                                <div class="w-20">
                                                    @livewire('good-button', ['memo' => $memo_data, 'isGood' => $goodMemoIds->contains($memo_data->id)], key('good-button-'.microtime(true)))
                                                </div>
                                                <div class="w-20">
                                                    @livewire('later-read-button', ['memo' => $memo_data, 'isLaterRead' => $laterReadMemoIds->contains($memo_data->id)], key('later-read-button-'.microtime(true)))
                                                </div>
                                            </div>
                                            {{-- タグ --}}
                                            <div class="mt-8">
                                                @foreach ($memo_data->labels as $label)
                                                <div
                                                    class="inline-block px-3 py-1 m-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                                                    {{ $label->name }}</div>
                                                @endforeach
                                            </div>


                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="flex flex-col justify-between lg:ml-3 lg:col-span-3">
                                            {{-- shortMemo --}}
                                            <div class="flex">
                                                <p class="mb-3 leading-relaxed break-all">
                                                    {!! nl2br(e($memo_data->shortMemo)) !!}
                                                </p>
                                            </div>

                                            {{-- ボタン --}}
                                            <div class="self-center mt-6 w-3/4 text-center lg:text-left">
                                                @if ($memo_data['user_id'] === Auth::id() )
                                                <button
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 lg:px-5 focus:outline-none hover:bg-indigo-500"
                                                    onclick="location.href='{{ route('group.memo_edit.edit', ['memo_id' => $memo_data['id'], 'type' => 'book'] ) }}' ">編集する</button>
                                                @else
                                                <div class=""></div>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="grid grid-cols-5">
                                            <div class="col-span-5">
                                                <div class="m-auto max-w-xs">
                                                    <div class="hidden text-right lg:block">
                                                        <i class="text-xl fas fa-book-open"></i>
                                                    </div>
                                                    <div class="flex justify-center">
                                                        @if(!(empty($memo_data->book_type_feature->book_photo_path)))
                                                        <img class="h-36 lg:h-auto"
                                                            src="{{ asset('storage/book-image/'. basename($memo_data->book_type_feature->book_photo_path)) }}" />
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-12 mt-4">
                                        <div class="col-span-11 rounded-lg border-2">
                                            <p class="break-all">
                                                @if ($memo_data->additionalMemo)
                                                {!! nl2br(e($memo_data->additionalMemo)) !!}
                                                @endif
                                            </p>
                                        </div>

                                        <!-- 三点リーダー（モーダル） -->
                                        <div class="flex justify-end items-end">
                                            <x-dropdown align="right" width="48">
                                                <x-slot name="trigger">
                                                    <button
                                                        class="flex border-2 border-transparent transition focus:outline-none">
                                                        <i class="text-xl fas fa-ellipsis-v"></i>
                                                    </button>
                                                </x-slot>

                                                <!-- モーダルの中身 -->
                                                <x-slot name="content">
                                                    <div class="flex flex-col px-4 text-gray-800">
                                                        <button onclick="Livewire.emit('showModalReportMemo')"
                                                            class="block p-2 w-full text-left hover:bg-slate-100">
                                                            メモを通報
                                                        </button>

                                                        @if (auth()->id() === $memo_data->user_id)
                                                            <button
                                                            onclick="if (confirm('本当に削除しますか？')) { @this.call('deleteMemo', {{ $memo_data->id }}) }"
                                                            class="block p-2 w-full text-left hover:bg-slate-100">
                                                            メモを削除
                                                            </button>
                                                        @endif

                                                    </div>
                                                </x-slot>
                                            </x-dropdown>
                                        </div>

                                    </div>

                                    {{-- メモ通報モーダルのトグルボタン --}}
                                    @can('admin-higher')
                                    @if ( $memo_data->reports_count )
                                    <div class="flex justify-center">
                                        <button x-on:click="report_memo = !report_memo"
                                            class="pt-5 text-gray-600 hover:text-black"><span
                                                x-show="!report_memo">通報内容を見る（{{$memo_data->reports_count}}件）</span>
                                            <span x-show="report_memo">通報内容を隠す（{{$memo_data->reports_count}}件）</span>
                                        </button>
                                    </div>
                                    @endif
                                    @endcan

                                </div>


                            </div>
                        </div>
                    </div>
                    {{-- メモ通報モーダル --}}
                    @livewire('report-memo', ['memo_id' => $memo_data->id])
                </section>
                @endif

                @can('admin-higher')
                @if ($all_memo_reports_data_paginated->isNotEmpty())
                {{-- 通報（メモ）セクション --}}
                <div x-show="report_memo" x-cloak class="py-10 bg-red-100">
                    <h2 class="px-5 pb-10 text-base font-bold">通報（メモ）</h2>
                    <div class="grid gap-10">
                        @foreach ($all_memo_reports_data_paginated as $memo_report_data)
                        <section class="text-gray-600 body-font">
                            <div class="px-5 mx-auto">
                                <div class="-m-4">
                                    <div class="p-4">
                                        <div
                                            class="overflow-hidden relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md">
                                            <div class="grid w-full sm:grid-cols-12">
                                                {{-- 左側 --}}
                                                <div class="flex content-center items-center sm:col-span-8">
                                                    {{-- photo --}}
                                                    @if($memo_report_data->contribute_user->profile_photo_path)
                                                    <button class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                        onclick="location.href='{{ route('admin.user_show',['user_id'=> $memo_report_data->contribute_user_id]) }}' ">
                                                        <img class="object-fill w-10 h-10 rounded-full"
                                                            src="{{ asset('storage/'. $memo_report_data->contribute_user->profile_photo_path) }}" />
                                                    </button>
                                                    @else
                                                    <button
                                                        class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                        onclick="location.href='{{ route('admin.user_show',['user_id' => $memo_report_data->contribute_user_id]) }}' ">
                                                        <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                    </button>
                                                    @endif
                                                    {{-- コメント作成者情報 --}}
                                                    <div>
                                                        <div>
                                                            <button class="block ml-3 text-left text-black"
                                                                type="button"
                                                                onclick="location.href='{{ route('admin.user_show',['user_id' => $memo_report_data->contribute_user_id]) }}' ">
                                                                {{ $memo_report_data->contribute_user->nickname }}
                                                            </button>
                                                            <button class="ml-5 text-left text-gray-500"
                                                                onclick="location.href='{{ route('admin.user_show',['user_id' => $memo_report_data->contribute_user_id]) }}' ">
                                                                {{ $memo_report_data->contribute_user->username }}
                                                            </button>
                                                        </div>
                                                        <div class="inline mt-1 ml-5 text-gray-500">
                                                            <i class="fa-regular fa-clock"></i>
                                                            <span>{{ $memo_report_data->created_at->format('Y-m-d')
                                                                }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- 右側 --}}
                                                <div class="mt-5 sm:mt-0 sm:text-right sm:col-span-4">
                                                    @if ($memo_report_data->reason == 1)
                                                    <p>法律違反</p>
                                                    @elseif ($memo_report_data->reason == 2)
                                                    <p>不適切なコンテンツ</p>
                                                    @elseif ($memo_report_data->reason == 3)
                                                    <p>フィッシング or スパム</p>
                                                    @elseif ($memo_report_data->reason == 4)
                                                    <p>その他</p>
                                                    @endif
                                                </div>

                                            </div>
                                            <div class="grid grid-cols-12 mt-4">
                                                <div class="col-span-11">
                                                    <p>{!! nl2br(e($memo_report_data->detail)) !!}
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
                    {{-- 通報（メモ） の ページネーション --}}
                    <div class="flex justify-center mt-10" x-cloak x-show="report_memo">
                        {{ $all_memo_reports_data_paginated->withQueryString()->links() }}
                    </div>
                </div>
                @endif
                @endcan

                <h2 class="px-5 text-base font-bold">コメント</h2>
                {{-- コメントセクション --}}
                @foreach ($comments_data_paginated as $comment_data)
                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md sm:px-8">
                                    <div class="w-max">
                                        {{-- 左側 --}}
                                        <div class="flex content-center items-center">
                                            {{-- photo --}}
                                            @if($comment_data->user->profile_photo_path)
                                            <button class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $comment_data->memo['group_id'] ,'user_id' => $comment_data['user_id']]) }}' ">
                                                <img class="object-fill w-10 h-10 rounded-full"
                                                    src="{{ asset('storage/'. $comment_data->user->profile_photo_path) }}" />
                                            </button>
                                            @else
                                            <button
                                                class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                onclick="location.href='{{ route('group.member_show', ['group_id' => $comment_data->memo['group_id'] ,'user_id' => $comment_data['user_id']]) }}' ">
                                                <img src="{{ asset('images/svg/default-user.svg') }}" />
                                            </button>
                                            @endif
                                            {{-- コメント作成者情報 --}}
                                            <div>
                                                <div class="mb-1 sm:mb-0">
                                                    <button
                                                        onclick="location.href='{{ route('group.member_show', ['group_id' => $comment_data->memo['group_id'] ,'user_id' => $comment_data['user_id']]) }}' "
                                                        class="block ml-3 text-black">
                                                        {{ $comment_data->user->nickname }}
                                                    </button>
                                                    <button class="ml-5 text-gray-500"
                                                        onclick="location.href='{{ route('group.member_show', ['group_id' => $comment_data->memo['group_id'] ,'user_id' => $comment_data['user_id']]) }}' ">
                                                        {{ $comment_data->user->username }}
                                                    </button>
                                                </div>
                                                <div class="inline mt-1 ml-5 text-gray-500">
                                                    <i class="fa-regular fa-clock"></i>
                                                    <span>{{ $comment_data['created_at']->format('Y-m-d')}}</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="grid grid-cols-12 mt-4">
                                        <div class="col-span-11">
                                            <p>{!! nl2br(e($comment_data->comment)) !!}</p>
                                        </div>

                                        <!-- 三点リーダー（モーダル） -->
                                        <div class="flex justify-end items-end">
                                            <x-dropdown align="right" width="48">
                                                <x-slot name="trigger">
                                                    <button
                                                        class="flex border-2 border-transparent transition focus:outline-none">
                                                        <i class="text-xl fas fa-ellipsis-v"></i>
                                                    </button>
                                                </x-slot>

                                                <!-- モーダルの中身 -->
                                                <x-slot name="content">
                                                    <div class="flex flex-col px-4 text-gray-800">

                                                        <button
                                                            onclick="Livewire.emit('showModalReportComment', {{ $comment_data->id }})"
                                                            class="block p-2 w-full text-left hover:bg-slate-100">
                                                            コメントを通報
                                                        </button>

                                                        @if (auth()->id() === $comment_data->user_id)
                                                            <button
                                                            onclick="if (confirm('本当に削除しますか？')) { @this.call('deleteComment', {{ $comment_data->id }}) }"
                                                            class="block p-2 w-full text-left hover:bg-slate-100">
                                                            コメントを削除する
                                                            </button>
                                                        @endif

                                                    </div>
                                                </x-slot>
                                            </x-dropdown>
                                        </div>

                                    </div>
                                    {{-- コメント通報モーダルのトグルボタン --}}
                                    @can('admin-higher')
                                    @if ( $comment_data->reports_count )
                                    <div class="flex justify-center">
                                        <button wire:click="toggleCommentReport({{ $comment_data->id }})"
                                            class="pt-5 text-gray-600 hover:text-black"><span
                                                x-show="!$wire.show_reports_comments[{{ $comment_data->id }}]"
                                                x-cloak>通報内容を見る（{{$comment_data->reports_count}}件）</span>
                                            <span x-show="$wire.show_reports_comments[{{ $comment_data->id }}]"
                                                x-cloak>通報内容を隠す（{{$comment_data->reports_count}}件）</span>
                                        </button>
                                    </div>
                                    @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- コメント通報モーダル --}}
                    @livewire('report-comment', ['comment_id' => $comment_data->id],
                    key('report-comment-'.microtime(true)))

                    @can('admin-higher')
                    @if ($comment_data->reports->isNotEmpty())
                    {{-- 通報（コメント）セクション --}}
                    @php
                        $reports = $comment_data->reports()->with('contribute_user')->paginate(5, ['*'], 'commentReportPage' . $comment_data->id);
                    @endphp
                    <div x-show="$wire.show_reports_comments[{{ $comment_data->id }}]" x-cloak
                        class="py-10 bg-orange-100">
                        <h2 class="px-5 pb-10 text-base font-bold">通報（コメント）</h2>
                        <div class="grid gap-10">
                            @foreach ($reports as $comment_report)
                            <section class="text-gray-600 body-font">
                                <div class="px-5 mx-auto">
                                    <div class="-m-4">
                                        <div class="p-4">
                                            <div
                                                class="overflow-hidden relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md">
                                                <div class="grid w-full sm:grid-cols-12">
                                                    {{-- 左側 --}}
                                                    <div class="flex content-center items-center sm:col-span-8">
                                                        {{-- photo --}}
                                                        @if($comment_report->contribute_user->profile_photo_path)
                                                        <button
                                                            class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                            onclick="location.href='{{ route('admin.user_show',['user_id'=> $comment_report->contribute_user_id]) }}' ">
                                                            <img class="object-fill w-10 h-10 rounded-full"
                                                                src="{{ asset('storage/'. $comment_report->contribute_user->profile_photo_path) }}" />
                                                        </button>
                                                        @else
                                                        <button
                                                            class="object-cover mr-3 w-10 h-10 bg-center rounded-full"
                                                            onclick="location.href='{{ route('admin.user_show',['user_id' => $comment_report->contribute_user_id]) }}' ">
                                                            <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                        </button>
                                                        @endif
                                                        {{-- コメント作成者情報 --}}
                                                        <div>
                                                            <div>
                                                                <button class="block ml-3 text-left text-black"
                                                                    type="button"
                                                                    onclick="location.href='{{ route('admin.user_show',['user_id' => $comment_report->contribute_user_id]) }}' ">
                                                                    {{ $comment_report->contribute_user->nickname }}
                                                                </button>
                                                                <button class="ml-5 text-left text-gray-500"
                                                                    onclick="location.href='{{ route('admin.user_show',['user_id' => $comment_report->contribute_user_id]) }}' ">
                                                                    {{ $comment_report->contribute_user->username }}
                                                                </button>
                                                            </div>
                                                            <div class="inline mt-1 ml-5 text-gray-500">
                                                                <i class="fa-regular fa-clock"></i>
                                                                <span>{{ $comment_report->created_at->format('Y-m-d')
                                                                    }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- 右側 --}}
                                                    <div class="mt-5 sm:mt-0 sm:text-right sm:col-span-4">
                                                        @if ($comment_report->reason == 1)
                                                        <p>法律違反</p>
                                                        @elseif ($comment_report->reason == 2)
                                                        <p>不適切なコンテンツ</p>
                                                        @elseif ($comment_report->reason == 3)
                                                        <p>フィッシング or スパム</p>
                                                        @elseif ($comment_report->reason == 4)
                                                        <p>その他</p>
                                                        @endif
                                                    </div>

                                                </div>
                                                <div class="grid grid-cols-12 mt-4">
                                                    <div class="col-span-11">
                                                        <p>{!! nl2br(e($comment_report->detail)) !!}
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
                        {{-- 通報（コメント） の ページネーション --}}
                        <div class="flex justify-center mt-10">
                            {{ $reports->links() }}
                        </div>
                    </div>
                    @endif
                    @endcan

                </section>
                @endforeach

                {{-- コメントのページネーション --}}
                    <div class="flex justify-center mt-10">
                        {{ $comments_data_paginated->withQueryString()->links() }}
                    </div>

                {{-- コメント入力セクション --}}

                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="overflow-hidden relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md sm:px-8">
                                    <div class="w-max">
                                        {{-- 左側 --}}
                                        <div class="flex items-center sm:items-start sm:content-center">
                                            {{-- photo --}}
                                            <div class="object-cover mr-3 w-10 h-10 bg-center rounded-full">
                                                @if (Auth::user()->profile_photo_path)
                                                <img class="object-fill w-10 h-10 rounded-full"
                                                    src="{{ Auth::user()->profile_photo_url }}"
                                                    alt="{{ Auth::user()->name }}" />
                                                @else
                                                <img src="{{ asset('images/svg/default-user.svg') }}" />
                                                @endif
                                            </div>

                                            <div class="self-center text-sm">
                                                <p class="ml-3 text-black">
                                                    コメントする
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                    @error('comment')
                                    <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                    @enderror
                                    <form wire:submit.prevent="storeComment"
                                        class="flex flex-col gap-10 justify-center mt-4 sm:gap-0 sm:grid sm:grid-cols-1">


                                        <div class="flex justify-center sm:block sm:col-span-7">
                                            <textarea wire:model.defer="comment" type="text" class="w-full rounded-lg"
                                                rows="1"></textarea>
                                        </div>
                                        <div class="flex justify-center text-right sm:block sm:col-span-5">
                                            <button
                                                class="px-6 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
                                                type="submit">コメントする</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

        </div>
    </div>

    {{-- ページに①通常アクセス or ②「戻るボタン」でアクセス　した際、 １：input（text）２：selectbox ３：textarea ４：checkbox ５：radiobutton をリセットするための処理
    --}}
    <script>
        function resetFormElements() {
        const selectElements = document.querySelectorAll('select.max-w-xs');
        const inputElements = document.querySelectorAll('input:not([name="_token"])');
        const textareaElements = document.querySelectorAll('textarea');

        selectElements.forEach(select => {
            select.value = '';
        });
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