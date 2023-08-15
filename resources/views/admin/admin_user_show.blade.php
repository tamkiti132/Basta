<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
      運営ユーザー一覧
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">

        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="grid gap-10 px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:gap-7 rounded-2xl">
                  {{-- ユーザー / 利用停止中 切り替え --}}
                  <div class="border-b border-gray-400">
                    <div class="flex text-xs font-bold sm:w-1/2 sm:text-lg">
                      <button
                        class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                        type="button" x-on:click="user = true; suspension_user = false"
                        x-bind:class="user ? 'border-b-4 border-blue-300' :'' ">
                        <p>ユーザー</p>
                      </button>
                      <button
                        class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                        type="button" x-on:click="user = false; suspension_user = true"
                        x-bind:class="suspension_user ? 'border-b-4 border-blue-300' :'' ">
                        <p>利用停止中のユーザー</p>
                      </button>
                    </div>
                  </div>

                  {{-- ユーザーの場合 --}}
                  <div class="grid gap-10 sm:gap-7" x-cloak x-show="user">
                    {{-- １人分のまとまり --}}
                    <div class="grid items-center gap-3 sm:gap-0 sm:grid-cols-7">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-2">
                        <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                        <p>運営太郎</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500">
                          @rruiiiquiqyuqryuqy
                        </p>
                      </div>
                      {{-- メールアドレス ・ 三点リーダー（モーダル） --}}
                      <div class="flex items-center justify-between sm:col-span-3">
                        <p class="text-sm text-gray-500">
                          test3@test8.com
                        </p>
                        <!-- 三点リーダー（モーダル） -->
                        <div class="flex justify-end">
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
                    <div class="grid items-center gap-3 sm:gap-0 sm:grid-cols-7">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-2">
                        <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                        <p>運営太郎</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500">
                          @rruiiiquiqyuqryuqy
                        </p>
                      </div>
                      {{-- メールアドレス ・ 三点リーダー（モーダル） --}}
                      <div class="flex items-center justify-between sm:col-span-3">
                        <p class="text-sm text-gray-500">
                          test3@test8.com
                        </p>
                        <!-- 三点リーダー（モーダル） -->
                        <div class="flex justify-end">
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
                    <div class="grid items-center gap-3 sm:gap-0 sm:grid-cols-7">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-2">
                        <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                        <p>運営太郎</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500">
                          @rruiiiquiqyuqryuqy
                        </p>
                      </div>
                      {{-- メールアドレス ・ 三点リーダー（モーダル） --}}
                      <div class="flex items-center justify-between sm:col-span-3">
                        <p class="text-sm text-gray-500">
                          test3@test8.com
                        </p>
                        <!-- 三点リーダー（モーダル） -->
                        <div class="flex justify-end">
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
                    <div class="grid items-center gap-3 sm:gap-0 sm:grid-cols-7">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-2">
                        <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                        <p>運営太郎</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500">
                          @rruiiiquiqyuqryuqy
                        </p>
                      </div>
                      {{-- メールアドレス ・ 三点リーダー（モーダル） --}}
                      <div class="flex items-center justify-between sm:col-span-3">
                        <p class="text-sm text-gray-500">
                          test3@test8.com
                        </p>
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
                    <div class="grid items-center gap-3 sm:gap-0 sm:grid-cols-7">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-2">
                        <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                        <p>運営太郎</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500">
                          @rruiiiquiqyuqryuqy
                        </p>
                      </div>
                      {{-- メールアドレス ・ 三点リーダー（モーダル） --}}
                      <div class="flex items-center justify-between sm:col-span-3">
                        <p class="text-sm text-gray-500">
                          test3@test8.com
                        </p>
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
          </div>
        </section>


      </div>
    </div>
  </div>

</x-app-layout>