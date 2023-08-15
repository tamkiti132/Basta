<x-app-layout>
  <x-slot name="header">
    <div class="grid items-center grid-cols-2">
      {{-- 左側 --}}
      <div class="flex">
        <button class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"
          onclick="location.href='{{ route('group.index') }}' "></button>
        <div>
          <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
            あつしーず
          </h2>
          <p class="ml-5 text-sm text-gray-500">
            管理者 : あつーし
          </p>
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
              <button class="block p-2 text-left hover:bg-slate-100">グループを削除</button>
              <button class="block p-2 text-left hover:bg-slate-100">グループを利用停止</button>
              <button class="block p-2 text-left hover:bg-slate-100">グループを利用停止解除</button>
            </div>
          </x-slot>
        </x-dropdown>
      </div>

    </div>
  </x-slot>

  <div class="py-12" x-data="{group_report: true,
                              member: false}">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="grid py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
        {{-- グループ通報情報 / メンバー 切り替え --}}
        <div class="mx-3 mb-10 border-b border-gray-400">
          <div class="flex text-xs font-bold sm:w-1/2 sm:text-lg">
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="group_report = true; member = false"
              x-bind:class="group_report ? 'border-b-4 border-blue-300' :'' ">
              <p>グループ通報情報</p>
            </button>
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="group_report = false; member = true"
              x-bind:class="member ? 'border-b-4 border-blue-300' :'' ">
              <p>メンバー</p>
            </button>
          </div>
        </div>
        {{-- グループ通報情報の場合 --}}
        <div class="grid gap-10" x-cloak x-show="group_report">
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
                        <button class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-20 sm:h-20"
                          onclick="location.href='{{ route('admin/user_show') }}' "></button>
                        {{-- コメント作成者情報 --}}
                        <div>
                          <div class="grid sm:grid-cols-2">
                            <p class="ml-3 text-black">
                              yamada
                            </p>
                            <button class="ml-5 text-sm text-gray-500"
                              onclick="location.href='{{ route('admin/user_show') }}' ">
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
                        <p>法律違反</p>
                      </div>

                    </div>
                    <div class="grid grid-cols-12 mt-4">
                      <div class="col-span-11 text-xs sm:text-base">
                        <p>このグループは、〜〜〜の法律に違反した活動をしております<br>
                          具体的には、〜〜〜といったことをしております。</p>
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
              <div class="-m-4">
                <div class="p-4">
                  <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                    <div class="grid w-full sm:grid-cols-12">
                      {{-- 左側 --}}
                      <div class="flex items-center content-center sm:col-span-8">
                        {{-- photo --}}
                        <div class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-20 sm:h-20"></div>
                        {{-- コメント作成者情報 --}}
                        <div>
                          <div class="grid sm:grid-cols-2">
                            <p class="ml-3 text-black">
                              yamada
                            </p>
                            <p class="ml-5 text-sm text-gray-500">
                              @fajofurelnoroqhnkhkau
                            </p>
                          </div>
                          <div class="inline mt-1 ml-5 text-sm text-gray-500">
                            <i class="fa-regular fa-clock"></i>
                            <span>2023/03/23</span>
                          </div>
                        </div>
                      </div>
                      {{-- 右側 --}}
                      <div class="mt-5 text-sm sm:text-base sm:mt-0 sm:text-right sm:col-span-4">
                        <p>法律違反</p>
                      </div>

                    </div>
                    <div class="grid grid-cols-12 mt-4">
                      <div class="col-span-11 text-xs sm:text-base">
                        <p>このグループは、〜〜〜の法律に違反した活動をしております<br>
                          具体的には、〜〜〜といったことをしております。</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        {{-- メンバーの場合 --}}
        <div class="grid gap-10" x-cloak x-show="member" x-data="{user: true,
                                                                  suspension_user: false}">
          {{-- ユーザーの場合 --}}
          <section class="text-gray-600 body-font">
            <div>
              <div class="px-4">
                <div class="grid gap-10 px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:gap-7 rounded-2xl">
                  {{-- ユーザー / 利用停止中ユーザー　切り替え --}}
                  <div class="mb-2 border-b border-gray-400">
                    <div class="flex text-xs font-bold sm:w-1/2 sm:text-lg">
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
                  {{-- 項目名 --}}
                  <div class="items-center hidden grid-cols-12 text-xs sm:grid mb-7">
                    {{-- プロフィール画像 ・ ニックネーム --}}
                    <div class="flex items-center col-span-3">
                      <p class="ml-12">ニックネーム</p>
                    </div>
                    {{-- ユーザーid --}}
                    <div class="col-span-3">
                      <p class="text-sm text-gray-500">
                        ユーザー名
                      </p>
                    </div>
                    {{-- メールアドレス --}}
                    <div class="col-span-3">
                      <p class="text-sm text-gray-500">
                        メールアドレス
                      </p>
                    </div>
                    <div class="col-span-1">
                      <p class="text-sm">権限</p>
                    </div>
                    {{-- ユーザー通報 --}}
                    <div class="grid grid-cols-5 col-span-2">
                      <div class="col-span-4 grid-rows-2">
                        <div class="text-center border-b-2 border-gray-300">
                          <p class="tracking-widest">通 報 数</p>
                        </div>
                        <div class="grid grid-cols-3 text-center">
                          <p>ユーザー</p>
                          {{-- 通報メモ --}}
                          <p>メモ</p>
                          {{-- 通報コメント --}}
                          <p>コメント</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  {{-- ユーザーの場合 --}}
                  <div class="grid gap-10 sm:gap-7" x-cloak x-show="user">
                    {{-- １人分のまとまり --}}
                    <div class="items-center sm:grid sm:grid-cols-12">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-3">
                        <button class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"
                          onclick="location.href='{{ route('admin/user_show') }}' "></button>
                        <p>はらだー</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="ml-16 sm:ml-0 sm:col-span-3">
                        <button class="text-sm text-gray-500" onclick="location.href='{{ route('admin/user_show') }}' ">
                          @harada-
                        </button>
                      </div>
                      {{-- メールアドレス --}}
                      <div class="mt-3 sm:col-span-3 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          test@test.com
                        </p>
                      </div>
                      {{-- 権限 --}}
                      <div class="mt-3 sm:col-span-1 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          管理者
                        </p>
                      </div>
                      <div class="grid grid-cols-3 sm:grid-cols-5 sm:col-span-2">
                        {{-- ユーザー通報 --}}
                        <div
                          class="grid grid-cols-3 col-span-2 mt-3 text-sm text-left sm:col-span-4 sm:text-center sm:mt-0">
                          <div class="col-span-2 sm:hidden">
                            <p>ユーザー通報</p>
                            <p>通報メモ</p>
                            <p>通報コメント</p>
                          </div>
                          <div class="grid sm:items-center sm:grid-cols-3 sm:col-span-3">
                            <p><span class="mr-3 sm:hidden">：</span>0</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                          </div>
                        </div>
                        <!-- 三点リーダー（モーダル） -->
                        <div class="flex items-end justify-end col-span-1">
                          <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                              <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                <i class="text-3xl fas fa-ellipsis-v"></i>
                              </button>
                            </x-slot>

                            <!-- モーダルの中身 -->
                            <x-slot name="content">
                              <div class="flex flex-col px-4 text-gray-800">
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを削除</button>
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを利用停止</button>
                              </div>
                            </x-slot>
                          </x-dropdown>
                        </div>
                      </div>
                    </div>

                    {{-- １人分のまとまり --}}
                    <div class="items-center sm:grid sm:grid-cols-12">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-3">
                        <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                        <p>はらだー</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="ml-16 sm:ml-0 sm:col-span-3">
                        <p class="text-sm text-gray-500">
                          @harada-
                        </p>
                      </div>
                      {{-- メールアドレス --}}
                      <div class="mt-3 sm:col-span-3 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          test@test.com
                        </p>
                      </div>
                      {{-- 権限 --}}
                      <div class="mt-3 sm:col-span-1 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          管理者
                        </p>
                      </div>
                      <div class="grid grid-cols-3 sm:grid-cols-5 sm:col-span-2">
                        {{-- ユーザー通報 --}}
                        <div
                          class="grid grid-cols-3 col-span-2 mt-3 text-sm text-left sm:col-span-4 sm:text-center sm:mt-0">
                          <div class="col-span-2 sm:hidden">
                            <p>ユーザー通報</p>
                            <p>通報メモ</p>
                            <p>通報コメント</p>
                          </div>
                          <div class="grid sm:items-center sm:grid-cols-3 sm:col-span-3">
                            <p><span class="mr-3 sm:hidden">：</span>0</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                          </div>
                        </div>
                        <!-- 三点リーダー（モーダル） -->
                        <div class="flex items-end justify-end col-span-1">
                          <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                              <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                <i class="text-3xl fas fa-ellipsis-v"></i>
                              </button>
                            </x-slot>

                            <!-- モーダルの中身 -->
                            <x-slot name="content">
                              <div class="flex flex-col px-4 text-gray-800">
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを削除</button>
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを利用停止</button>
                              </div>
                            </x-slot>
                          </x-dropdown>
                        </div>
                      </div>
                    </div>

                    {{-- １人分のまとまり --}}
                    <div class="items-center sm:grid sm:grid-cols-12">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-3">
                        <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                        <p>はらだー</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="ml-16 sm:ml-0 sm:col-span-3">
                        <p class="text-sm text-gray-500">
                          @harada-
                        </p>
                      </div>
                      {{-- メールアドレス --}}
                      <div class="mt-3 sm:col-span-3 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          test@test.com
                        </p>
                      </div>
                      {{-- 権限 --}}
                      <div class="mt-3 sm:col-span-1 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          管理者
                        </p>
                      </div>
                      <div class="grid grid-cols-3 sm:grid-cols-5 sm:col-span-2">
                        {{-- ユーザー通報 --}}
                        <div
                          class="grid grid-cols-3 col-span-2 mt-3 text-sm text-left sm:col-span-4 sm:text-center sm:mt-0">
                          <div class="col-span-2 sm:hidden">
                            <p>ユーザー通報</p>
                            <p>通報メモ</p>
                            <p>通報コメント</p>
                          </div>
                          <div class="grid sm:items-center sm:grid-cols-3 sm:col-span-3">
                            <p><span class="mr-3 sm:hidden">：</span>0</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                          </div>
                        </div>
                        <!-- 三点リーダー（モーダル） -->
                        <div class="flex items-end justify-end col-span-1">
                          <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                              <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                <i class="text-3xl fas fa-ellipsis-v"></i>
                              </button>
                            </x-slot>

                            <!-- モーダルの中身 -->
                            <x-slot name="content">
                              <div class="flex flex-col px-4 text-gray-800">
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを削除</button>
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを利用停止</button>
                              </div>
                            </x-slot>
                          </x-dropdown>
                        </div>
                      </div>
                    </div>
                  </div>

                  {{-- 利用停止中ユーザーの場合 --}}
                  <div class="grid gap-10 sm:gap-7" x-cloak x-show="suspension_user">
                    {{-- １人分のまとまり --}}
                    <div class="items-center sm:grid sm:grid-cols-12">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-3">
                        <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                        <p>はらだー（利用停止中）</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="ml-16 sm:ml-0 sm:col-span-3">
                        <p class="text-sm text-gray-500">
                          @harada-
                        </p>
                      </div>
                      {{-- メールアドレス --}}
                      <div class="mt-3 sm:col-span-3 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          test@test.com
                        </p>
                      </div>
                      {{-- 権限 --}}
                      <div class="mt-3 sm:col-span-1 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          管理者
                        </p>
                      </div>
                      <div class="grid grid-cols-3 sm:grid-cols-5 sm:col-span-2">
                        {{-- ユーザー通報 --}}
                        <div
                          class="grid grid-cols-3 col-span-2 mt-3 text-sm text-left sm:col-span-4 sm:text-center sm:mt-0">
                          <div class="col-span-2 sm:hidden">
                            <p>ユーザー通報</p>
                            <p>通報メモ</p>
                            <p>通報コメント</p>
                          </div>
                          <div class="grid sm:items-center sm:grid-cols-3 sm:col-span-3">
                            <p><span class="mr-3 sm:hidden">：</span>0</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                          </div>
                        </div>
                        <!-- 三点リーダー（モーダル） -->
                        <div class="flex items-center justify-end gap-20">
                          <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                              <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                <i class="text-3xl fas fa-ellipsis-v"></i>
                              </button>
                            </x-slot>

                            <!-- モーダルの中身 -->
                            <x-slot name="content">
                              <div class="flex flex-col text-gray-800">
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを削除</button>
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを利用停止解除</button>
                              </div>
                            </x-slot>
                          </x-dropdown>
                        </div>
                      </div>
                    </div>

                    {{-- １人分のまとまり --}}
                    <div class="items-center sm:grid sm:grid-cols-12">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-3">
                        <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                        <p>はらだー</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="ml-16 sm:ml-0 sm:col-span-3">
                        <p class="text-sm text-gray-500">
                          @harada-
                        </p>
                      </div>
                      {{-- メールアドレス --}}
                      <div class="mt-3 sm:col-span-3 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          test@test.com
                        </p>
                      </div>
                      {{-- 権限 --}}
                      <div class="mt-3 sm:col-span-1 sm:mt-0">
                        <p class="text-sm text-gray-500">
                          管理者
                        </p>
                      </div>
                      <div class="grid grid-cols-3 sm:grid-cols-5 sm:col-span-2">
                        {{-- ユーザー通報 --}}
                        <div
                          class="grid grid-cols-3 col-span-2 mt-3 text-sm text-left sm:col-span-4 sm:text-center sm:mt-0">
                          <div class="col-span-2 sm:hidden">
                            <p>ユーザー通報</p>
                            <p>通報メモ</p>
                            <p>通報コメント</p>
                          </div>
                          <div class="grid sm:items-center sm:grid-cols-3 sm:col-span-3">
                            <p><span class="mr-3 sm:hidden">：</span>0</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                            <p><span class="mr-3 sm:hidden">：</span>1</p>
                          </div>
                        </div>
                        <!-- 三点リーダー（モーダル） -->
                        <div class="flex items-center justify-end gap-20">
                          <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                              <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                                <i class="text-3xl fas fa-ellipsis-v"></i>
                              </button>
                            </x-slot>

                            <!-- モーダルの中身 -->
                            <x-slot name="content">
                              <div class="flex flex-col text-gray-800">
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを削除</button>
                                <button class="block p-2 text-left hover:bg-slate-100">ユーザーを利用停止解除</button>
                              </div>
                            </x-slot>
                          </x-dropdown>
                        </div>
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
  </div>

</x-app-layout>