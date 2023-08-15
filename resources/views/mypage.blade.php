<x-app-layout>
  <x-slot name="header">
    <div class="flex">
      <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
        マイページ
      </h2>
      <p class="text-sm ml-14 sm:ml-28">{{ $count_all_my_memos_data }}<span class="ml-3">投稿</span></p>
    </div>
  </x-slot>


  <div class="flex flex-col-reverse gap-8 pt-12 mx-auto lg:flex-row max-w-7xl sm:px-6 lg:px-8">
    <div>
      <form method="get" action="{{ route('mypage.show', ['user_id' => Auth::id()]) }}" class="text-left">
        <input type="text" name="search" placeholder="タイトルかメモ概要のワードで検索" class="rounded-xl" size="50">
        <button class="px-3 py-2 font-bold">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
      </form>
    </div>

    <select onchange="window.location.href = '/mypage/{{ Auth::id() }}/' + this.value" class="max-w-xs rounded-xl">
      <option value="">グループで絞り込み</option>
      @foreach($user_groups as $group)
      <option value="{{ $group->id }}" {{ (request()->route('group_id')==$group->id) ? 'selected' : '' }}>
        {{ $group->name }}
      </option>
      @endforeach
    </select>
  </div>

  <div class="py-12 xl:grid-cols-12 xl:grid">
    {{-- ラベル一覧（左） --}}
    <div class="absolute z-20 col-span-2 sm:block xl:static">
      <input type="checkbox" id="drawer-toggle" class="sr-only peer" checked>
      <label for="drawer-toggle" class="left-0 inline-block p-2 bg-indigo-500 rounded-lg xl:hidden top-40 ">
        <div class="w-6 h-1 mb-3 bg-white rounded-lg"></div>
        <div class="w-6 h-1 bg-white rounded-lg"></div>
      </label>
      <div class="hidden h-full bg-white shadow-lg rounded-r-2xl peer-checked:block">

        <div class="z-20 px-1 py-2 sm:static">
          {{-- ラベル表示 --}}
          <div class="xl:col-span-2">
            <button class="flex items-center w-full gap-4 p-2 hover:bg-slate-100">
              <i class="text-lg sm:text-3xl fas fa-globe fa-fw"></i>
              <p class="text-xs sm:text-base">Webサイト</p>
            </button>
            <button class="flex items-center w-full gap-4 p-2 hover:bg-slate-100">
              <i class="text-lg sm:text-3xl fas fa-book-open fa-fw"></i>
              <p class="text-xs sm:text-base">本</p>
            </button>
            <button class="flex items-center w-full gap-4 p-2 hover:bg-slate-100">
              <span class="sm:text-4xl material-symbols-rounded" style="font-variation-settings:'FILL' 1">label</span>
              <p class="text-xs sm:text-base">プログラミング</p>
            </button>
            <button class="flex items-center w-full gap-4 p-2 hover:bg-slate-100">
              <span class="sm:text-4xl material-symbols-rounded" style="font-variation-settings:'FILL' 1">label</span>
              <p class="text-xs sm:text-base">設計</p>
            </button>
            <button class="flex items-center w-full gap-4 p-2 hover:bg-slate-100">
              <span class="sm:text-4xl material-symbols-rounded">label</span>
              <p class="text-xs sm:text-base">Laravel</p>
            </button>

            {{-- ラベル編集 --}}
            <div>
              <div>
                <button x-on:click="modal_label_edit = true"
                  class="flex items-center w-full gap-4 p-2 hover:bg-slate-100"><i
                    class="sm:text-2xl fa-solid fa-pencil fa-fw"></i>
                  <p class="text-sm sm:text-base">ラベルを編集</p>
                </button>
              </div>

              {{-- ラベル編集モーダル --}}
              <div x-cloak x-show="modal_label_edit"
                class="fixed top-0 left-0 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
                <div x-on:click.away="modal_label_edit = false"
                  class="flex flex-col w-full h-auto max-w-xs px-3 py-2 bg-white rounded-xl">
                  <div class="flex items-end justify-between pb-2 mb-2 border-b border-black">
                    <p>こんにチワワ</p>
                    <div x-on:click="modal_label_edit = false" class="text-2xl cursor-pointer">+</div>
                  </div>
                  <div class="flex flex-col py-2">
                    {{-- ラベル1個分 --}}
                    <div class="flex justify-between">
                      <button class="w-full py-2 text-left hover:bg-slate-100">
                        <p class="text-xs sm:text-base">プログラミング</p>
                      </button>
                      <button class="px-2 hover:bg-slate-100">
                        <i class="fa-regular fa-trash-can"></i>
                      </button>
                    </div>
                    {{-- ラベル1個分 --}}
                    <div class="flex justify-between">
                      <button class="w-full py-2 text-left hover:bg-slate-100">
                        <p class="text-xs sm:text-base">設計</p>
                      </button>
                      <button class="px-2 hover:bg-slate-100">
                        <i class="fa-regular fa-trash-can"></i>
                      </button>
                    </div>
                    {{-- ラベル1個分 --}}
                    <div class="flex justify-between">
                      <button class="w-full py-2 text-left hover:bg-slate-100">
                        <p class="text-xs sm:text-base">Laravel</p>
                      </button>
                      <button class="px-2 hover:bg-slate-100">
                        <i class="fa-regular fa-trash-can"></i>
                      </button>
                    </div>

                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- メインコンテンツ（中央） --}}
    <div class="w-full mx-auto max-w-7xl sm:px-6 lg:px-8 xl:col-span-8">
      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
        {{-- 自分が作成したメモ / いいねしたメモ / あとでよむにしたメモ 選択--}}
        <div class="mx-3 mb-10 border-b border-gray-400">
          <div class="flex text-xs font-bold sm:text-lg lg:w-1/2">
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="my_memo = true; good_memo = false; later_read_memo = false"
              x-bind:class="my_memo ? 'border-b-4 border-blue-300' :'' ">
              <p>自分が作成したメモ</p>
            </button>
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="my_memo = false; good_memo = true; later_read_memo = false"
              x-bind:class="good_memo ? 'border-b-4 border-blue-300' :'' ">
              <p>いいねしたメモ</p>
            </button>
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="my_memo = false; good_memo = false; later_read_memo = true"
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
            <div class="container px-5 mx-auto">
              <div class="-m-4 ">
                <div class="p-4">
                  <div class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                    <div class="grid gap-10 sm:grid-cols-7 sm:gap-0">
                      {{-- 左側 --}}
                      <div class="sm:col-span-3">
                        <div class="flex items-center content-center">
                          {{-- メモ作成者情報 --}}
                          <div class="text-xs">
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-regular fa-clock"></i>
                              <span>{{ $memo_data['created_at']->format('Y-m-d') }}</span>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                              <span>{{ $memo_data['updated_at']->format('Y-m-d')}}</span>
                            </div>
                          </div>
                        </div>
                        {{-- メモタイトル --}}
                        <div class="mt-5 ml-3 leading-none y-4">
                          <button class="font-bold text-left text-gray-700 break-all sm:text-xl title-font"
                            onclick="location.href='{{ route('group.memo_show.show', ['id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                            $memo_data['title'] }}
                          </button>
                        </div>
                        {{-- 『いいね』 『あとでよむ』 --}}
                        <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                          <div class="w-20">
                            @livewire('good-button', ['memo' => $memo_data])
                          </div>
                          <div class="w-20">
                            @livewire('later-read-button', ['memo' => $memo_data])
                          </div>
                        </div>
                        {{-- タグ --}}
                        <div class="mt-8 text-xs sm:text-sm">
                          @foreach ($memo_data->labels as $label)
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                            {{ $label->name }}</div>
                          @endforeach
                        </div>


                      </div>
                      {{-- 真ん中 --}}
                      <div class="sm:col-span-3">
                        {{-- shortMemo --}}
                        <div class="flex">
                          <p class="mb-3 text-sm leading-relaxed break-all sm:text-base">{!!
                            nl2br(e($memo_data['shortMemo']))
                            !!}
                          </p>
                        </div>

                        {{-- ボタン --}}
                        <div class="mt-6 text-center sm:mt-40 sm:ml-8 sm:text-right">
                          <button
                            class="px-10 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                            onclick="window.open('{{ $memo_data['url'] }}') ">リンクを開く</button>
                        </div>
                      </div>
                      {{-- 右側 --}}
                      <div class="hidden sm:grid-cols-5 sm:grid">
                        <div class="sm:col-span-2">
                        </div>
                        <div class="sm:col-span-3">
                          <div class="text-right">
                            <i class="text-3xl fas fa-globe"></i>
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
            <div class="container px-5 mx-auto">
              <div class="-m-4 ">
                <div class="p-4">
                  <div class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                    <div class="grid gap-10 sm:grid-cols-7 sm:gap-0">
                      {{-- 左側 --}}
                      <div class="sm:col-span-3">
                        <div class="flex items-center content-center">
                          {{-- メモ作成者情報 --}}
                          <div class="text-xs">
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-regular fa-clock"></i>
                              <span>{{ $memo_data['created_at']->format('Y-m-d') }}</span>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                              <span>{{ $memo_data['updated_at']->format('Y-m-d')}}</span>
                            </div>
                          </div>
                        </div>
                        {{-- メモタイトル --}}
                        <div class="mt-5 ml-3 leading-none y-4">
                          <button class="font-bold text-left text-gray-700 break-all sm:text-xl title-font"
                            onclick="location.href='{{ route('group.memo_show.show', ['id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                            $memo_data['title'] }}
                          </button>
                        </div>
                        {{-- 『いいね』 『あとでよむ』 --}}
                        <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                          <div class="w-20">
                            @livewire('good-button', ['memo' => $memo_data])
                          </div>
                          <div class="w-20">
                            @livewire('later-read-button', ['memo' => $memo_data])
                          </div>
                        </div>
                        {{-- タグ --}}
                        <div class="mt-8 text-xs sm:text-sm">
                          @foreach ($memo_data->labels as $label)
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                            {{ $label->name }}</div>
                          @endforeach
                        </div>


                      </div>
                      {{-- 真ん中 --}}
                      <div class="sm:col-span-3">
                        {{-- shortMemo --}}
                        <div class="flex">
                          <p class="mb-3 text-sm leading-relaxed break-all sm:text-base">
                            {!! nl2br(e($memo_data['shortMemo'])) !!}
                          </p>
                        </div>
                      </div>
                      {{-- 右側 --}}
                      <div class="grid grid-cols-5">
                        {{-- <div class="col-span-2">
                        </div> --}}
                        <div class="col-span-5">
                          <div class="max-w-xs m-auto">
                            <div class="hidden text-right xl:block">
                              <i class="text-3xl fas fa-book-open"></i>
                            </div>
                            {{-- <img src="/images/本の画像（青）.png"> --}}
                            @if($memo_data['book_photo_path'])
                            <img src="{{ asset('storage/book-image/'. $memo_data['book_photo_path']) }}" />
                            @endif
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
            <div class="container px-5 mx-auto">
              <div class="-m-4 ">
                <div class="p-4">
                  <div class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                    <div class="grid gap-10 sm:grid-cols-7 sm:gap-0">
                      {{-- 左側 --}}
                      <div class="sm:col-span-3">
                        <div class="flex items-center content-center">
                          {{-- photo --}}
                          @if($memo_data->profile_photo_path)
                          <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data->memo_user_id]) }}' ">
                            <img class="object-fill rounded-full h-14 w-14"
                              src="{{ asset('storage/'. $memo_data->profile_photo_path) }}" />
                          </button>
                          @else
                          <button class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"
                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data->memo_user_id]) }}' "></button>
                          @endif
                          {{-- メモ作成者情報 --}}
                          <div class="text-xs">
                            <div class="mb-1 sm:mb-0">
                              <p class="ml-3 text-black sm:text-base">
                                {{ $memo_data['nickname'] }}
                              </p>
                              <button class="ml-5 text-gray-500 sm:text-sm"
                                onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' ">
                                {{ $memo_data['username'] }}
                              </button>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-regular fa-clock"></i>
                              <span>
                                {{ $memo_data['created_at']->format('Y-m-d') }}
                              </span>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                              <span>
                                {{ $memo_data['updated_at']->format('Y-m-d')}}
                              </span>
                            </div>
                          </div>
                        </div>
                        {{-- メモタイトル --}}
                        <div class="mt-5 ml-3 leading-none y-4">
                          <button class="font-bold text-left text-gray-700 break-all sm:text-xl title-font"
                            onclick="location.href='{{ route('group.memo_show.show', ['id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                            $memo_data['title'] }}
                          </button>
                        </div>
                        {{-- 『いいね』 『あとでよむ』 --}}
                        <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                          <div class="w-20">
                            @livewire('good-button', ['memo' => $memo_data])
                          </div>
                          <div class="w-20">
                            @livewire('later-read-button', ['memo' => $memo_data])
                          </div>
                        </div>
                        {{-- タグ --}}
                        <div class="mt-8 text-xs sm:text-sm">
                          @foreach ($memo_data->labels as $label)
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                            {{ $label->name }}</div>
                          @endforeach
                        </div>


                      </div>
                      {{-- 真ん中 --}}
                      <div class="sm:col-span-3">
                        {{-- shortMemo --}}
                        <div class="flex">
                          <p class="mb-3 text-sm leading-relaxed break-all sm:text-base">
                            {!! nl2br(e($memo_data['shortMemo'])) !!}
                          </p>
                        </div>

                        {{-- ボタン --}}
                        <div class="mt-6 text-center sm:mt-40 sm:ml-8 sm:text-right">
                          <button
                            class="px-10 py-3 font-bold text-white bg-indigo-400 border-0 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500">リンクを開く</button>
                        </div>
                      </div>
                      {{-- 右側 --}}
                      <div class="hidden sm:grid-cols-5 sm:grid">
                        <div class="sm:col-span-2">
                        </div>
                        <div class="sm:col-span-3">
                          <div class="text-right">
                            <i class="text-3xl fas fa-globe"></i>
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
            <div class="container px-5 mx-auto">
              <div class="-m-4 ">
                <div class="p-4">
                  <div class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                    <div class="grid gap-10 sm:grid-cols-7 sm:gap-0">
                      {{-- 左側 --}}
                      <div class="sm:col-span-3">
                        <div class="flex items-center content-center">
                          {{-- photo --}}
                          @if($memo_data->profile_photo_path)
                          <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data->memo_user_id]) }}' ">
                            <img class="object-fill rounded-full h-14 w-14"
                              src="{{ asset('storage/'. $memo_data->profile_photo_path) }}" />
                          </button>
                          @else
                          <button class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"
                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data->memo_user_id]) }}' "></button>
                          @endif
                          {{-- メモ作成者情報 --}}
                          <div class="text-xs">
                            <div class="mb-1 sm:mb-0">
                              <p class="ml-3 text-black sm:text-base">
                                {{ $memo_data['nickname'] }}
                              </p>
                              <button class="ml-5 text-gray-500 sm:text-sm"
                                onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' ">
                                {{ $memo_data['username'] }}
                              </button>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-regular fa-clock"></i>
                              <span>
                                {{ $memo_data['created_at']->format('Y-m-d') }}
                              </span>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                              <span>
                                {{ $memo_data['updated_at']->format('Y-m-d')}}
                              </span>
                            </div>
                          </div>
                        </div>
                        {{-- メモタイトル --}}
                        <div class="mt-5 ml-3 leading-none y-4">
                          <button class="font-bold text-left text-gray-700 break-all sm:text-xl title-font"
                            onclick="location.href='{{ route('group.memo_show.show', ['id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                            $memo_data['title'] }}
                          </button>
                        </div>
                        {{-- 『いいね』 『あとでよむ』 --}}
                        <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                          <div class="w-20">
                            @livewire('good-button', ['memo' => $memo_data])
                          </div>
                          <div class="w-20">
                            @livewire('later-read-button', ['memo' => $memo_data])
                          </div>
                        </div>
                        {{-- タグ --}}
                        <div class="mt-8 text-xs sm:text-sm">
                          @foreach ($memo_data->labels as $label)
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                            {{ $label->name }}</div>
                          @endforeach
                        </div>


                      </div>
                      {{-- 真ん中 --}}
                      <div class="sm:col-span-3">
                        {{-- shortMemo --}}
                        <div class="flex">
                          <p class="mb-3 text-sm leading-relaxed break-all sm:text-base">
                            {!! nl2br(e($memo_data['shortMemo'])) !!}
                          </p>
                        </div>
                      </div>
                      {{-- 右側 --}}
                      <div class="grid grid-cols-5">
                        {{-- <div class="col-span-2">
                        </div> --}}
                        <div class="col-span-5">
                          <div class="max-w-xs m-auto">
                            <div class="hidden text-right xl:block">
                              <i class="text-3xl fas fa-book-open"></i>
                            </div>
                            @if($memo_data['book_photo_path'])
                            <img src="{{ asset('storage/book-image/'. $memo_data['book_photo_path']) }}" />
                            @endif
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
            <div class="container px-5 mx-auto">
              <div class="-m-4 ">
                <div class="p-4">
                  <div class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                    <div class="grid gap-10 sm:grid-cols-7 sm:gap-0">
                      {{-- 左側 --}}
                      <div class="sm:col-span-3">
                        <div class="flex items-center content-center">
                          {{-- photo --}}
                          @if($memo_data->profile_photo_path)
                          <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data->memo_user_id]) }}' ">
                            <img class="object-fill rounded-full h-14 w-14"
                              src="{{ asset('storage/'. $memo_data->profile_photo_path) }}" />
                          </button>
                          @else
                          <button class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"
                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data->memo_user_id]) }}' "></button>
                          @endif
                          {{-- メモ作成者情報 --}}
                          <div class="text-xs">
                            <div class="mb-1 sm:mb-0">
                              <p class="ml-3 text-black sm:text-base">
                                {{ $memo_data['nickname'] }}
                              </p>
                              <button class="ml-5 text-gray-500 sm:text-sm"
                                onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' ">
                                {{ $memo_data['username'] }}
                              </button>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-regular fa-clock"></i>
                              <span>
                                {{ $memo_data['created_at']->format('Y-m-d') }}
                              </span>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                              <span>
                                {{ $memo_data['updated_at']->format('Y-m-d')}}
                              </span>
                            </div>
                          </div>
                        </div>
                        {{-- メモタイトル --}}
                        <div class="mt-5 ml-3 leading-none y-4">
                          <button class="font-bold text-left text-gray-700 break-all sm:text-xl title-font"
                            onclick="location.href='{{ route('group.memo_show.show', ['id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                            $memo_data['title'] }}
                          </button>
                        </div>
                        {{-- 『いいね』 『あとでよむ』 --}}
                        <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                          <div class="w-20">
                            @livewire('good-button', ['memo' => $memo_data])
                          </div>
                          <div class="w-20">
                            @livewire('later-read-button', ['memo' => $memo_data])
                          </div>
                        </div>
                        {{-- タグ --}}
                        <div class="mt-8 text-xs sm:text-sm">
                          @foreach ($memo_data->labels as $label)
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                            {{ $label->name }}</div>
                          @endforeach
                        </div>


                      </div>
                      {{-- 真ん中 --}}
                      <div class="sm:col-span-3">
                        {{-- shortMemo --}}
                        <div class="flex">
                          <p class="mb-3 text-sm leading-relaxed break-all sm:text-base">
                            {!! nl2br(e($memo_data['shortMemo'])) !!}
                          </p>
                        </div>

                        {{-- ボタン --}}
                        <div class="mt-6 text-center sm:mt-40 sm:ml-8 sm:text-right">
                          <button
                            class="px-10 py-3 font-bold text-white bg-indigo-400 border-0 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500">リンクを開く</button>
                        </div>
                      </div>
                      {{-- 右側 --}}
                      <div class="hidden sm:grid-cols-5 sm:grid">
                        <div class="sm:col-span-2">
                        </div>
                        <div class="sm:col-span-3">
                          <div class="text-right">
                            <i class="text-3xl fas fa-globe"></i>
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
            <div class="container px-5 mx-auto">
              <div class="-m-4 ">
                <div class="p-4">
                  <div class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                    <div class="grid gap-10 sm:grid-cols-7 sm:gap-0">
                      {{-- 左側 --}}
                      <div class="sm:col-span-3">
                        <div class="flex items-center content-center">
                          {{-- photo --}}
                          @if($memo_data->profile_photo_path)
                          <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data->memo_user_id]) }}' ">
                            <img class="object-fill rounded-full h-14 w-14"
                              src="{{ asset('storage/'. $memo_data->profile_photo_path) }}" />
                          </button>
                          @else
                          <button class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"
                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data->memo_user_id]) }}' "></button>
                          @endif
                          {{-- メモ作成者情報 --}}
                          <div class="text-xs">
                            <div class="mb-1 sm:mb-0">
                              <p class="ml-3 text-black sm:text-base">
                                {{ $memo_data['nickname'] }}
                              </p>
                              <button class="ml-5 text-gray-500 sm:text-sm"
                                onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['memo_user_id']]) }}' ">
                                {{ $memo_data['username'] }}
                              </button>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-regular fa-clock"></i>
                              <span>
                                {{ $memo_data['created_at']->format('Y-m-d') }}
                              </span>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                              <span>
                                {{ $memo_data['updated_at']->format('Y-m-d')}}
                              </span>
                            </div>
                          </div>
                        </div>
                        {{-- メモタイトル --}}
                        <div class="mt-5 ml-3 leading-none y-4">
                          <button class="font-bold text-left text-gray-700 break-all sm:text-xl title-font"
                            onclick="location.href='{{ route('group.memo_show.show', ['id' => $memo_data->id, 'group_id' => $memo_data->group_id, 'type' => 'book'] ) }}' ">{{
                            $memo_data['title'] }}
                          </button>
                        </div>
                        {{-- 『いいね』 『あとでよむ』 --}}
                        <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                          <div class="w-20">
                            @livewire('good-button', ['memo' => $memo_data])
                          </div>
                          <div class="w-20">
                            @livewire('later-read-button', ['memo' => $memo_data])
                          </div>
                        </div>
                        {{-- タグ --}}
                        <div class="mt-8 text-xs sm:text-sm">
                          @foreach ($memo_data->labels as $label)
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                            {{ $label->name }}</div>
                          @endforeach
                        </div>


                      </div>
                      {{-- 真ん中 --}}
                      <div class="sm:col-span-3">
                        {{-- shortMemo --}}
                        <div class="flex">
                          <p class="mb-3 text-sm leading-relaxed break-all sm:text-base">
                            {!! nl2br(e($memo_data['shortMemo'])) !!}
                          </p>
                        </div>
                      </div>
                      {{-- 右側 --}}
                      <div class="grid grid-cols-5">
                        {{-- <div class="col-span-2">
                        </div> --}}
                        <div class="col-span-5">
                          <div class="max-w-xs m-auto">
                            <div class="hidden text-right xl:block">
                              <i class="text-3xl fas fa-book-open"></i>
                            </div>
                            @if($memo_data['book_photo_path'])
                            <img src="{{ asset('storage/book-image/'. $memo_data['book_photo_path']) }}" />
                            @endif
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


</x-app-layout>