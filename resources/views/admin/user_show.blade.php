<x-app-layout>
  <x-slot name="header">
    <div class="grid items-center grid-cols-2">
      {{-- 左側 --}}
      <div class="flex">
        <button class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></button>
        <div>
          <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
            はらだー
          </h2>
          <button class="ml-5 text-sm text-gray-500">
            @harada-
          </button>
        </div>
      </div>
      {{-- 右側 --}}
      <div class="flex items-center justify-end gap-20">
        <x-dropdown align="right" width="48">
          <x-slot name="trigger">
            <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
              <i class="px-5 text-3xl fas fa-ellipsis-v"></i>
            </button>
          </x-slot>

          <!-- モーダルの中身 -->
          <x-slot name="content">
            <div class="flex flex-col text-gray-800">
              <button class="block p-2 text-left hover:bg-slate-100">ユーザーを削除</button>
              <button class="block p-2 text-left hover:bg-slate-100">ユーザーを利用停止</button>
              <button class="block p-2 text-left hover:bg-slate-100">ユーザーを利用停止解除</button>
            </div>
          </x-slot>
        </x-dropdown>
      </div>

    </div>
  </x-slot>

  <div class="py-12" x-data="{report_user: true,
                              report_memo: false,
                              report_comment: false}" x-bind:class="report_memo ? '2xl:grid-cols-12 2xl:grid' :'' ">

    {{-- ラベル一覧（左） --}}
    <div class="absolute z-20 col-span-2 sm:block xl:static" x-cloak x-show="report_memo">
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

    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" x-bind:class="report_memo ? '2xl:col-span-8' :'' ">
      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
        {{-- ユーザー通報情報 / メモ / コメント 切り替え　--}}
        <div class="mx-3 mb-10 border-b border-gray-400">
          <div class="flex text-xs font-bold sm:text-lg sm:w-1/2">
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="report_user = true; report_memo = false; report_comment = false"
              x-bind:class="report_user ? 'border-b-4 border-blue-300' :'' ">
              <p>ユーザー通報情報</p>
            </button>
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="report_user = false; report_memo = true; report_comment = false"
              x-bind:class="report_memo ? 'border-b-4 border-blue-300' :'' ">
              <p>メモ</p>
            </button>
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="report_user = false; report_memo = false; report_comment = true"
              x-bind:class="report_comment ? 'border-b-4 border-blue-300' :'' ">
              <p>コメント</p>
            </button>
          </div>
        </div>
        {{-- ユーザー通報情報 の場合--}}
        <div class="grid gap-10" x-cloak x-show="report_user">
          {{-- @php
          dd($reports_data);
          @endphp --}}

          @foreach ($reports_data->target_report_type_0 as $report_data)
          {{-- 通報１つ分 --}}
          <section class="text-gray-600 body-font">
            <div class="container px-5 mx-auto">
              <div class="-m-4">
                <div class="p-4">
                  <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                    <div class="grid w-full sm:grid-cols-12">
                      {{-- 左側 --}}
                      <div class="flex items-center content-center sm:col-span-8">
                        {{-- photo --}}
                        @if($report_data->contribute_user->profile_photo_path)
                        <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                          onclick="location.href='{{-- route('group.member.show',['id'=>$report_data['memo_user_id']]) --}}' ">
                          <img class="object-fill rounded-full h-14 w-14"
                            src="{{ asset('storage/'. $report_data->contribute_user->profile_photo_path) }}" />
                        </button>
                        @else
                        <button class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"
                          onclick="location.href='{{-- route('group.member.show',['id'=>$report_data['memo_user_id']]) --}}' "></button>
                        @endif
                        {{-- コメント作成者情報 --}}
                        <div>
                          <div class="grid sm:grid-cols-2">
                            <p class="ml-3 text-black">
                              {{ $report_data->contribute_user->nickname }}
                            </p>
                            <button class="ml-5 text-sm text-gray-500"
                              onclick="location.href='{{-- route('admin.user_show.show') --}}' ">
                              {{ $report_data->contribute_user->username }}
                            </button>
                          </div>
                          <div class="inline mt-1 ml-5 text-sm text-gray-500">
                            <i class="fa-regular fa-clock"></i>
                            <span>{{ $report_data->created_at->format('Y-m-d') }}</span>
                          </div>
                        </div>
                      </div>
                      {{-- 右側 --}}
                      <div class="mt-5 text-sm sm:text-base sm:mt-0 sm:text-right sm:col-span-4">
                        @if ($report_data->reason == 0)
                        <p>法律違反</p>
                        @elseif ($report_data->reason == 1)
                        <p>不適切なコンテンツ</p>
                        @elseif ($report_data->reason == 2)
                        <p>フィッシング or スパム</p>
                        @elseif ($report_data->reason == 3)
                        <p>その他</p>
                        @endif
                      </div>

                    </div>
                    <div class="grid grid-cols-12 mt-4">
                      <div class="col-span-11 text-xs sm:text-base">
                        <p>{{ $report_data->detail }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
          @endforeach
        </div>

        {{-- メモ の場合--}}
        <div class="grid gap-10" x-cloak x-show="report_memo">
          @foreach ($reports_data->target_report_type_1 as $report_data)
          {{-- 通報１つ分 --}}
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
                            <div class="grid mb-1 sm:grid-cols-2 sm:mb-0">
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-regular fa-clock"></i>
                              <span>2023/03/23</span>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                              <span>2023/03/23</span>
                            </div>
                          </div>
                        </div>
                        {{-- メモタイトル --}}
                        <div class="mt-5 ml-3 leading-none y-4">
                          <button class="self-center font-bold text-gray-700 sm:text-xl title-font"
                            onclick="location.href='{{-- route('group.memo_show') --}}' ">画面仕様書の書き方のコツ
                          </button>
                        </div>
                        {{-- 『いいね』 『あとでよむ』 --}}
                        <div class="grid w-20 grid-cols-3 mt-5 ml-3 gap-14">
                          <div class="w-20">
                            <i class="inline sm:text-lg fa-solid fa-bell"></i>
                            <span class="ml-1">6</span>
                          </div>
                          <div class="w-20">
                            <i class="inline sm:text-lg fa-regular fa-heart"></i>
                            <span class="ml-1">1</span>
                          </div>
                          <div class="w-20">
                            <i class="inline sm:text-lg fa-regular fa-file"></i>
                            <span class="ml-2">1</span>
                          </div>
                        </div>
                        {{-- タグ --}}
                        <div class="mt-8 text-xs sm:text-sm">
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                            プログラミング</div>
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">設計
                          </div>
                        </div>


                      </div>
                      {{-- 真ん中 --}}
                      <div class="sm:col-span-3">
                        {{-- shortMemo --}}
                        <div class="flex">
                          <p class="mb-3 text-sm leading-relaxed sm:text-base">画面仕様書の書き方についてすごくわかりやすくまとめられてました。おすすめ!!
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

          {{-- 通報１つ分 --}}
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
                          <div class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-20 sm:h-20"></div>
                          {{-- メモ作成者情報 --}}
                          <div class="text-xs">
                            <div class="grid mb-1 sm:grid-cols-2 sm:mb-0">
                              <p class="ml-3 text-black sm:text-base">
                                はらだー
                              </p>
                              <p class="ml-5 text-gray-500 sm:text-sm">
                                @harada-
                              </p>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-regular fa-clock"></i>
                              <span>2023/03/23</span>
                            </div>
                            <div class="inline mt-1 ml-5 text-gray-500 sm:text-sm">
                              <i class="fa-solid fa-rotate-right fa-rotate-270"></i>
                              <span>2023/03/23</span>
                            </div>
                          </div>
                        </div>
                        {{-- メモタイトル --}}
                        <div class="mt-5 ml-3 leading-none y-4">
                          <h1 class="self-center font-bold text-gray-700 sm:text-xl title-font">画面仕様書の書き方のコツ
                          </h1>
                        </div>
                        {{-- 『いいね』 『あとでよむ』 --}}
                        <div class="grid w-20 grid-cols-3 mt-5 ml-3 gap-14">
                          <div class="w-20">
                            <i class="inline sm:text-lg fa-solid fa-bell"></i>
                            <span class="ml-1">6</span>
                          </div>
                          <div class="w-20">
                            <i class="inline sm:text-lg fa-regular fa-heart"></i>
                            <span class="ml-1">1</span>
                          </div>
                          <div class="w-20">
                            <i class="inline sm:text-lg fa-regular fa-file"></i>
                            <span class="ml-2">1</span>
                          </div>
                        </div>
                        {{-- タグ --}}
                        <div class="mt-8 text-xs sm:text-sm">
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
                            プログラミング</div>
                          <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">設計
                          </div>
                        </div>


                      </div>
                      {{-- 真ん中 --}}
                      <div class="sm:col-span-3">
                        {{-- shortMemo --}}
                        <div class="flex">
                          <p class="mb-3 text-sm leading-relaxed sm:text-base">画面仕様書の書き方についてすごくわかりやすくまとめられてました。おすすめ!!
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
                            <img src="/images/本の画像（青）.png">
                          </div>
                        </div>
                      </div>
                    </div>


                  </div>
                </div>
              </div>
          </section>
          @endforeach
        </div>

        {{-- コメント の場合--}}
        <div class="grid gap-10" x-cloak x-show="report_comment">
          @foreach ($reports_data->target_report_type_2 as $report_data)
          {{-- 通報１つ分 --}}
          <section class="text-gray-600 body-font">
            <div class="container px-5 mx-auto">
              <div class="-m-4">
                <div class="p-4">
                  <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                    <div class="grid w-full sm:grid-cols-12">
                      {{-- 左側 --}}
                      <div class="flex items-center content-center sm:col-span-8">
                        {{-- photo --}}
                        @if($report_data->contribute_user->profile_photo_path)
                        <button class="object-cover mr-3 bg-center rounded-full h-14 w-14"
                          onclick="location.href='{{-- route('group.member.show',['id'=>$report_data['memo_user_id']]) --}}' ">
                          <img class="object-fill rounded-full h-14 w-14"
                            src="{{ asset('storage/'. $report_data->contribute_user->profile_photo_path) }}" />
                        </button>
                        @else
                        <button class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"
                          onclick="location.href='{{-- route('group.member.show',['id'=>$report_data['memo_user_id']]) --}}' "></button>
                        @endif
                        {{-- コメント作成者情報 --}}
                        <div>
                          <div class="grid sm:grid-cols-2">
                            <p class="ml-3 text-black">
                              {{ $report_data->contribute_user->nickname }}
                            </p>
                            <p class="ml-5 text-sm text-gray-500">
                              {{ $report_data->contribute_user->username }}
                            </p>
                          </div>
                          <div class="inline mt-1 ml-5 text-sm text-gray-500">
                            <i class="fa-regular fa-clock"></i>
                            <span>{{ $report_data->created_at->format('Y-m-d') }}</span>
                          </div>
                        </div>
                      </div>
                      {{-- 右側 --}}
                      <div class="mt-5 text-sm sm:text-base sm:mt-0 sm:text-right sm:col-span-4">
                        @if ($report_data->reason == 0)
                        <p>法律違反</p>
                        @elseif ($report_data->reason == 1)
                        <p>不適切なコンテンツ</p>
                        @elseif ($report_data->reason == 2)
                        <p>フィッシング or スパム</p>
                        @elseif ($report_data->reason == 3)
                        <p>その他</p>
                        @endif
                      </div>

                    </div>
                    <div class="grid grid-cols-12 mt-4">
                      <div class="col-span-11 text-xs sm:text-base">
                        <p>{{ $report_data->detail }}</p>
                      </div>
                      {{-- 通報数 --}}
                      <div class="flex flex-col-reverse text-right">
                        <div>
                          <i class="inline sm:text-lg fa-solid fa-bell"></i>
                          <span class="ml-1">10</span>
                        </div>
                      </div>
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
  </div>

</x-app-layout>