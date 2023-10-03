<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
      メモ詳細
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

      <x-flash-message status="suspension" />
      <x-flash-message status="error" />

      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
        @if ($memo_data->type == 0)
        {{-- メモセクション --}}
        {{-- Webタイプの　場合 --}}
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
                        @if($memo_data->user->profile_photo_path)
                        <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                          onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['user_id']]) }}' "><img
                            class="object-fill rounded-full h-14 w-14"
                            src="{{ asset('storage/'. $memo_data->user->profile_photo_path) }}" /></button>
                        @else
                        <div class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"></div>
                        @endif
                        {{-- メモ作成者情報 --}}
                        <div class="text-xs">
                          <div class="mb-1 sm:mb-0">
                            <p class="ml-3 text-black sm:text-base">
                              {{ $memo_data->user->nickname }}
                            </p>
                            <button class="ml-5 text-gray-500 sm:text-sm"
                              onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['user_id']]) }}' ">
                              {{ $memo_data->user->username }}
                            </button>
                          </div>
                          <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                            <i class="fa-regular fa-clock"></i>
                            <span>{{ $memo_data->created_at->format('Y-m-d') }}</span>
                          </div>
                          <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                            <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                            <span>{{ $memo_data->updated_at->format('Y-m-d')}}</span>
                          </div>
                        </div>
                      </div>
                      {{-- メモタイトル --}}
                      <div class="mt-5 ml-3 leading-none y-4">
                        <h1 class="self-center font-bold text-gray-700 break-all sm:text-xl title-font">{{
                          $memo_data->title }}
                        </h1>
                      </div>
                      {{-- 『いいね』 『あとでよむ』 --}}
                      <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                        <div class="w-20">
                          @livewire('good-button', ['memo' => $memo_data],
                          key('good-button-'.microtime(true)))
                        </div>
                        <div class="w-20">
                          @livewire('later-read-button', ['memo' => $memo_data],
                          key('later-read-button-'.microtime(true)))
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
                          nl2br(e($memo_data->shortMemo)) !!}
                        </p>
                      </div>

                      {{-- ボタン --}}
                      <div class="mt-6 text-center sm:mt-40 sm:ml-8 sm:text-right">
                        <button
                          class="px-10 py-3 font-bold text-white bg-indigo-400 border-0 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500"
                          onclick="window.open('{{ $memo_data->web_type_feature->url }}') ">リンクを開く</button>
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
                  <div class="grid grid-cols-12 mt-4">
                    <div class="col-span-11 text-xs border-2 rounded-lg sm:text-base">
                      <p class="break-all">
                        @if ($memo_data->additionalMemo)
                        {!! nl2br(e($memo_data->additionalMemo)) !!}
                        @endif
                      </p>
                    </div>

                    <!-- 三点リーダー（モーダル） -->
                    <div class="flex items-end justify-end">
                      <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                          <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                            <i class="text-3xl fas fa-ellipsis-v"></i>
                          </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                          <div class="flex flex-col px-4 text-gray-800">

                            <button onclick="Livewire.emit('showModalReportMemo')"
                              class="block w-full p-2 text-left hover:bg-slate-100">
                              メモを通報
                            </button>
                            <form method="POST"
                              action="{{ route('group.memo_show.destroyMemo', ['id' => $memo_data->id ]) }}">
                              @csrf
                              <input type="hidden" name="memo_id" value="{{ $memo_data->id }}">
                              <button type="submit" class="block p-2 text-left hover:bg-slate-100"
                                onclick="return confirm('本当に削除しますか？');">メモを削除</button>
                            </form>
                          </div>
                        </x-slot>
                      </x-dropdown>
                    </div>

                  </div>
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
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                  <div class="grid gap-10 sm:grid-cols-7 sm:gap-0">
                    {{-- 左側 --}}
                    <div class="sm:col-span-3">
                      <div class="flex items-center content-center">
                        {{-- photo --}}
                        @if($memo_data->user->profile_photo_path)
                        <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                          onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['user_id']]) }}' "><img
                            class="object-fill rounded-full h-14 w-14"
                            src="{{ asset('storage/'. $memo_data->user->profile_photo_path) }}" /></button>
                        @else
                        <div class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"></div>
                        @endif
                        {{-- メモ作成者情報 --}}
                        <div class="text-xs">
                          <div class="mb-1 sm:mb-0">
                            <p class="ml-3 text-black sm:text-base">
                              {{ $memo_data->user->nickname }}
                            </p>
                            <button class="ml-5 text-gray-500 sm:text-sm"
                              onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['user_id']]) }}' ">
                              {{ $memo_data->user->username }}
                            </button>
                          </div>
                          <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                            <i class="fa-regular fa-clock"></i>
                            <span>{{ $memo_data->created_at->format('Y-m-d') }}</span>
                          </div>
                          <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                            <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                            <span>{{ $memo_data->updated_at->format('Y-m-d')}}</span>
                          </div>
                        </div>
                      </div>
                      {{-- メモタイトル --}}
                      <div class="mt-5 ml-3 leading-none y-4">
                        <h1 class="self-center font-bold text-gray-700 break-all sm:text-xl title-font">{{
                          $memo_data->title }}
                        </h1>
                      </div>
                      {{-- 『いいね』 『あとでよむ』 --}}
                      <div class="grid w-20 grid-cols-2 gap-10 mt-5 ml-3 text-sm">
                        <div class="w-20">
                          @livewire('good-button', ['memo' => $memo_data],
                          key('good-button-'.microtime(true)))
                        </div>
                        <div class="w-20">
                          @livewire('later-read-button', ['memo' => $memo_data],
                          key('later-read-button-'.microtime(true)))
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
                          nl2br(e($memo_data->shortMemo)) !!}
                        </p>
                      </div>

                      {{-- ボタン --}}
                      <div class="mt-6 text-center sm:mt-40 sm:ml-8 sm:text-right">
                        {{-- <button
                          class="px-10 py-3 font-bold text-white bg-indigo-400 border-0 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500"
                          onclick="window.open('{{ $memo_data->web_type_feature->url }}') ">リンクを開く</button> --}}
                      </div>
                    </div>
                    {{-- 右側 --}}
                    <div class="grid grid-cols-5">
                      <div class="col-span-5">
                        <div class="max-w-xs m-auto">
                          <div class="hidden text-right xl:block">
                            <i class="text-3xl fas fa-book-open"></i>
                          </div>
                          {{-- <img src="/images/本の画像（青）.png"> --}}
                          @if(!(empty($memo_data->book_type_feature->book_photo_path)))
                          <img
                            src="{{ asset('storage/book-image/'. basename($memo_data->book_type_feature->book_photo_path)) }}" />
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="grid grid-cols-12 mt-4">
                    <div class="col-span-11 text-xs border-2 rounded-lg sm:text-base">
                      <p class="break-all">
                        @if ($memo_data->additionalMemo)
                        {!! nl2br(e($memo_data->additionalMemo)) !!}
                        @endif
                      </p>
                    </div>

                    <!-- 三点リーダー（モーダル） -->
                    <div class="flex items-end justify-end">
                      <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                          <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                            <i class="text-3xl fas fa-ellipsis-v"></i>
                          </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                          <div class="flex flex-col px-4 text-gray-800">

                            <button onclick="Livewire.emit('showModalReportMemo')"
                              class="block w-full p-2 text-left hover:bg-slate-100">
                              メモを通報
                            </button>

                            <form method="POST"
                              action="{{ route('group.memo_show.destroyMemo', ['id' => $memo_data->id ]) }}">
                              @csrf
                              <input type="hidden" name="memo_id" value="{{ $memo_data->id }}">
                              <button type="submit" class="block p-2 text-left hover:bg-slate-100"
                                onclick="return confirm('本当に削除しますか？');">メモを削除</button>
                            </form>

                          </div>
                        </x-slot>
                      </x-dropdown>
                    </div>

                  </div>
                </div>


              </div>
            </div>
          </div>
          {{-- メモ通報モーダル --}}
          @livewire('report-memo', ['memo_id' => $memo_data->id])
        </section>
        @endif

        <h2 class="px-5 text-lg font-bold">コメント</h2>
        {{-- コメントセクション --}}
        {{-- @php
        dd($comments_data);
        @endphp --}}
        @foreach ($comments_data as $comment_data)
        {{-- @php
        dd($comment_data);
        @endphp --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4">
              <div class="p-4">
                <div class="relative px-4 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                  <div class="w-max">
                    {{-- 左側 --}}
                    <div class="flex items-center content-center">
                      {{-- photo --}}
                      @if($comment_data->user->profile_photo_path)
                      <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                        onclick="location.href='{{ route('group.member.show', ['id' => $comment_data['user_id']]) }}' "><img
                          class="object-fill rounded-full h-14 w-14"
                          src="{{ asset('storage/'. $comment_data->user->profile_photo_path) }}" /></button>
                      @else
                      <button class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"
                        onclick="location.href='{{ route('group.member.show', ['id' => $comment_data['user_id']]) }}' "></button>
                      @endif
                      {{-- コメント作成者情報 --}}
                      <div class="text-xs">
                        <div class="mb-1 sm:mb-0">
                          <p class="ml-3 text-black sm:text-base">
                            {{ $comment_data->user->nickname }}
                          </p>
                          <button class="ml-5 text-sm text-gray-500 sm:text-sm"
                            onclick="location.href='{{ route('group.member.show', ['id' => $comment_data['id']]) }}' ">
                            {{ $comment_data->user->username }}
                          </button>
                        </div>
                        <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                          <i class="fa-regular fa-clock"></i>
                          <span>{{ $comment_data['created_at']->format('Y-m-d')}}</span>
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="grid grid-cols-12 mt-4">
                    <div class="col-span-11 text-sm sm:text-base">
                      <p>{!! nl2br(e($comment_data->comment)) !!}</p>
                    </div>

                    <!-- 三点リーダー（モーダル） -->
                    <div class="flex items-end justify-end">
                      <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                          <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                            <i class="text-3xl fas fa-ellipsis-v"></i>
                          </button>
                        </x-slot>

                        <!-- モーダルの中身 -->
                        <x-slot name="content">
                          <div class="flex flex-col px-4 text-gray-800">

                            <button onclick="Livewire.emit('showModalReportComment')"
                              class="block w-full p-2 text-left hover:bg-slate-100">
                              コメントを通報
                            </button>
                            <form method="POST"
                              action="{{ route('group.memo_show.destroyComment', ['id' => $comment_data->id ]) }}">
                              @csrf
                              <input type="hidden" name="memo_id" value="{{ $memo_data->id }}">
                              <button type="submit" class="block p-2 text-left hover:bg-slate-100"
                                onclick="return confirm('本当に削除しますか？');">コメントを削除する</button>
                            </form>
                          </div>
                        </x-slot>
                      </x-dropdown>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- コメント通報モーダル --}}
          @livewire('report-comment', ['comment_id' => $comment_data->id])
        </section>
        @endforeach

        {{-- コメント入力セクション --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4">
              <div class="p-4">
                <div
                  class="relative px-4 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                  <div class="w-max">
                    {{-- 左側 --}}
                    <div class="flex items-center sm:items-start sm:content-center">
                      {{-- photo --}}
                      <div class="object-cover mr-3 bg-center rounded-full h-14 w-14">
                        <img class="object-fill rounded-full h-14 w-14" src="{{ Auth::user()->profile_photo_url }}"
                          alt="{{ Auth::user()->name }}" />
                      </div>
                      {{-- コメント作成者情報 --}}
                      <div class="self-center text-sm sm:text-base">
                        <p class="ml-3 text-black">
                          コメントする
                        </p>
                      </div>
                    </div>

                  </div>
                  <form method="POST" action="{{ route('group.memo_show.store') }}"
                    class="flex flex-col justify-center gap-10 mt-4 sm:gap-0 sm:grid sm:grid-cols-1">
                    @csrf
                    <x-validation-error name="comment" />
                    <input type="hidden" name="memo_id" value="{{ $memo_data->id }}">
                    <div class="flex justify-center sm:block sm:col-span-7">
                      <textarea type="text" name="comment" class="rounded-lg sm:w-full" rows="1"></textarea>
                    </div>
                    <div class="flex justify-center text-right sm:block sm:col-span-5">
                      <button
                        class="px-6 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                        type="">コメントする</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>

        <h2 class="px-5 text-lg font-bold">通報（メモ）</h2>
        {{-- 通報（メモ）セクション --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  <div class="grid w-full sm:grid-cols-12">
                    {{-- 左側 --}}
                    <div class="flex items-center content-center sm:col-span-8">
                      {{-- photo --}}
                      <button class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-20 sm:h-20"
                        onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['user_id']]) }}' "></button>
                      {{-- コメント作成者情報 --}}
                      <div>
                        <div class="grid sm:grid-cols-2">
                          <p class="ml-3 text-black">
                            yamada
                          </p>
                          <button class="ml-5 text-sm text-gray-500"
                            onclick="location.href='{{ route('group.member.show', ['id' => $memo_data['user_id']]) }}' ">
                            @fajofurelnoroqhnkhkau
                          </button>
                        </div>
                        <div class="inline mt-1 ml-5 text-sm text-gray-500">
                          <i class="fa-regular fa-clock"></i>
                          <span>2023/03/23</span>
                        </div>
                      </div>
                    </div>
                    {{-- 右側 --}}
                    <div class="mt-5 text-sm sm:text-base sm:mt-0 sm:text-right sm:col-span-4">
                      <p>不適切なコンテンツ</p>
                    </div>

                  </div>
                  <div class="grid grid-cols-12 mt-4">
                    <div class="col-span-11 text-xs sm:text-base">
                      <p>手順1の、〜〜〜という記載が、〜〜〜という理由でよくないと思います。</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <h2 class="px-5 text-lg font-bold">通報（コメント）</h2>
        {{-- 通報（コメント）セクション --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  <div class="grid w-full sm:grid-cols-12">
                    {{-- 左側 --}}
                    <div class="flex items-center content-center sm:col-span-8">
                      {{-- photo --}}
                      <button class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-20 sm:h-20"
                        onclick="location.href='{{-- route('group.member') --}}' "></button>
                      {{-- コメント作成者情報 --}}
                      <div>
                        <div class="grid sm:grid-cols-2">
                          <p class="ml-3 text-black">
                            yamada
                          </p>
                          <button class="ml-5 text-sm text-gray-500"
                            onclick="location.href='{{-- route('group.member') --}}' ">
                            @fajofurelnoroqhnkhkau
                          </button>
                        </div>
                        <div class="inline mt-1 ml-5 text-sm text-gray-500">
                          <i class="fa-regular fa-clock"></i>
                          <span>2023/03/23</span>
                        </div>
                      </div>
                    </div>
                    {{-- 右側 --}}
                    <div class="mt-5 text-sm sm:text-base sm:mt-0 sm:text-right sm:col-span-4">
                      <p>不適切なコンテンツ</p>
                    </div>

                  </div>
                  <div class="grid grid-cols-12 mt-4">
                    <div class="col-span-11 text-xs sm:text-base">
                      <p>手順1の、〜〜〜という記載が、〜〜〜という理由でよくないと思います。</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

    </div>
  </div>

</x-app-layout>