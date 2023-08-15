<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
      グループ一覧
    </h2>
  </x-slot>

  <div class="py-12" x-data="{group: true,
                              suspension_group: false}">

    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
        {{-- グループ / 利用停止中グループ 切り替え　--}}
        <div class="mx-3 mb-10 border-b border-gray-400">
          <div class="flex text-xs font-bold sm:w-1/2 sm:text-lg">
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="group = true; suspension_group = false"
              x-bind:class="group ? 'border-b-4 border-blue-300' :'' ">
              <p>グループ</p>
            </button>
            <button class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
              type="button" x-on:click="group = false; suspension_group = true"
              x-bind:class="suspension_group ? 'border-b-4 border-blue-300' :'' ">
              <p>利用停止中グループ</p>
            </button>
          </div>
        </div>
        {{-- グループの場合 --}}
        <div class="grid gap-10" x-cloak x-show="group">
          {{-- グループ１つ分 --}}
          <section class="text-gray-600 body-font">
            <div class="container px-5 mx-auto">
              <div class="-m-4">
                <div class="p-4">
                  <div class="relative p-8 px-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                    <div class="grid sm:grid-cols-12 gap-y-10 sm:gap-y-0">
                      {{-- 左側 --}}
                      <div class="col-span-6">
                        <div class="flex content-center">
                          {{-- photo --}}
                          <button class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-20 sm:h-20"
                            onclick="location.href='{{ route('admin/group_show') }}' "></button>
                          {{-- グループ名 --}}
                          <button class="self-center font-bold text-gray-700 sm:text-base title-font"
                            onclick="location.href='{{ route('admin/group_show') }}' ">
                            あつしーず
                          </button>
                        </div>
                        <div class="mt-2 leading-none y-4 ">
                          <div class="grid pt-5 ml-3 sm:grid-cols-2">
                            <p class="text-sm leading-none text-gray-700">
                              管理者<span class="ml-4">：</span>あつーし
                            </p>
                            <p class="ml-20 text-xs text-gray-500 sm:text-sm sm:ml-0">
                              @eaofoajfaljlja
                            </p>
                          </div>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            通報数<span class="ml-4">：</span>3
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            メンバー<span class="ml-1">：</span>12
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            メモ<span class="ml-8">：</span>65
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            コメント<span class="ml-1">：</span>17
                          </p>
                        </div>
                      </div>
                      {{-- 真ん中 --}}
                      <div class="col-span-5 text-xs sm:text-base">
                        <p class="mb-3 leading-relaxed">主に、PHPやLaravelをはじめ、バックエンド周りを中心に学びを共有することが主な目的です!!</p>
                      </div>
                      {{-- 右側 --}}
                      <!-- 三点リーダー（モーダル） -->
                      <div class="flex items-end sm:justify-end">
                        <x-dropdown align="right" width="48">
                          <x-slot name="trigger">
                            <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                              <i class="text-3xl fas fa-ellipsis-v"></i>
                            </button>
                          </x-slot>

                          <!-- モーダルの中身 -->
                          <x-slot name="content">
                            <div class="flex flex-col px-4 text-gray-800">
                              <button class="block p-2 text-left hover:bg-slate-100">グループを削除</button>
                              <button class="block p-2 text-left hover:bg-slate-100">グループを利用停止</button>
                            </div>
                          </x-slot>
                        </x-dropdown>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </section>

          {{-- グループ１つ分 --}}
          <section class="text-gray-600 body-font">
            <div class="container px-5 mx-auto">
              <div class="-m-4">
                <div class="p-4">
                  <div class="relative p-8 px-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                    <div class="grid sm:grid-cols-12 gap-y-10 sm:gap-y-0">
                      {{-- 左側 --}}
                      <div class="col-span-6">
                        <div class="flex content-center">
                          {{-- photo --}}
                          <button class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-20 sm:h-20"
                            onclick="location.href='{{ route('admin/group_show') }}' "></button>
                          {{-- グループ名 --}}
                          <button class="self-center font-bold text-gray-700 sm:text-base title-font"
                            onclick="location.href='{{ route('admin/group_show') }}' ">
                            あつしーず
                          </button>
                        </div>
                        <div class="mt-2 leading-none y-4 ">
                          <div class="grid pt-5 ml-3 sm:grid-cols-2">
                            <p class="text-sm leading-none text-gray-700">
                              管理者<span class="ml-4">：</span>あつーし
                            </p>
                            <p class="ml-20 text-xs text-gray-500 sm:text-sm sm:ml-0">
                              @eaofoajfaljlja
                            </p>
                          </div>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            通報数<span class="ml-4">：</span>3
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            メンバー<span class="ml-1">：</span>12
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            メモ<span class="ml-8">：</span>65
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            コメント<span class="ml-1">：</span>17
                          </p>
                        </div>
                      </div>
                      {{-- 真ん中 --}}
                      <div class="col-span-5 text-xs sm:text-base">
                        <p class="mb-3 leading-relaxed">主に、PHPやLaravelをはじめ、バックエンド周りを中心に学びを共有することが主な目的です!!</p>
                      </div>
                      {{-- 右側 --}}
                      <!-- 三点リーダー（モーダル） -->
                      <div class="flex items-end sm:justify-end">
                        <x-dropdown align="right" width="48">
                          <x-slot name="trigger">
                            <button class="flex text-sm transition border-2 border-transparent focus:outline-none">
                              <i class="text-3xl fas fa-ellipsis-v"></i>
                            </button>
                          </x-slot>

                          <!-- モーダルの中身 -->
                          <x-slot name="content">
                            <div class="flex flex-col px-4 text-gray-800">
                              <button class="block p-2 text-left hover:bg-slate-100">グループを削除</button>
                              <button class="block p-2 text-left hover:bg-slate-100">グループを利用停止</button>
                            </div>
                          </x-slot>
                        </x-dropdown>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        {{-- 利用停止中グループの場合 --}}
        <div class="grid gap-10" x-cloak x-show="suspension_group">
          {{-- グループ１つ分 --}}
          <section class="text-gray-600 body-font">
            <div class="container px-5 mx-auto">
              <div class="-m-4">
                <div class="p-4">
                  <div class="relative p-8 px-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                    <div class="grid sm:grid-cols-12 gap-y-10 sm:gap-y-0">
                      {{-- 左側 --}}
                      <div class="col-span-6">
                        <div class="flex content-center">
                          {{-- photo --}}
                          <button class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-20 sm:h-20"
                            onclick="location.href='{{ route('admin/group_show') }}' "></button>
                          {{-- グループ名 --}}
                          <button class="self-center font-bold text-gray-700 sm:text-base title-font"
                            onclick="location.href='{{ route('admin/group_show') }}' ">
                            あつしーず
                          </button>
                        </div>
                        <div class="mt-2 leading-none y-4 ">
                          <div class="grid pt-5 ml-3 sm:grid-cols-2">
                            <p class="text-sm leading-none text-gray-700">
                              管理者<span class="ml-4">：</span>あつーし
                            </p>
                            <p class="ml-20 text-xs text-gray-500 sm:text-sm sm:ml-0">
                              @eaofoajfaljlja
                            </p>
                          </div>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            通報数<span class="ml-4">：</span>3
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            メンバー<span class="ml-1">：</span>12
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            メモ<span class="ml-8">：</span>65
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            コメント<span class="ml-1">：</span>17
                          </p>
                        </div>
                      </div>
                      {{-- 真ん中 --}}
                      <div class="col-span-5 text-xs sm:text-base">
                        <p class="mb-3 leading-relaxed">主に、PHPやLaravelをはじめ、バックエンド周りを中心に学びを共有することが主な目的です!!</p>
                      </div>
                      {{-- 右側 --}}
                      <!-- 三点リーダー（モーダル） -->
                      <div class="flex items-end sm:justify-end">
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
          </section>

          {{-- グループ１つ分 --}}
          <section class="text-gray-600 body-font">
            <div class="container px-5 mx-auto">
              <div class="-m-4">
                <div class="p-4">
                  <div class="relative p-8 px-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                    <div class="grid sm:grid-cols-12 gap-y-10 sm:gap-y-0">
                      {{-- 左側 --}}
                      <div class="col-span-6">
                        <div class="flex content-center">
                          {{-- photo --}}
                          <button class="object-cover w-8 h-8 mr-3 bg-blue-200 rounded-full sm:w-20 sm:h-20"
                            onclick="location.href='{{ route('admin/group_show') }}' "></button>
                          {{-- グループ名 --}}
                          <button class="self-center font-bold text-gray-700 sm:text-base title-font"
                            onclick="location.href='{{ route('admin/group_show') }}' ">
                            あつしーず
                          </button>
                        </div>
                        <div class="mt-2 leading-none y-4 ">
                          <div class="grid pt-5 ml-3 sm:grid-cols-2">
                            <p class="text-sm leading-none text-gray-700">
                              管理者<span class="ml-4">：</span>あつーし
                            </p>
                            <p class="ml-20 text-xs text-gray-500 sm:text-sm sm:ml-0">
                              @eaofoajfaljlja
                            </p>
                          </div>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            通報数<span class="ml-4">：</span>3
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            メンバー<span class="ml-1">：</span>12
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            メモ<span class="ml-8">：</span>65
                          </p>
                          <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                            コメント<span class="ml-1">：</span>17
                          </p>
                        </div>
                      </div>
                      {{-- 真ん中 --}}
                      <div class="col-span-5 text-xs sm:text-base">
                        <p class="mb-3 leading-relaxed">主に、PHPやLaravelをはじめ、バックエンド周りを中心に学びを共有することが主な目的です!!</p>
                      </div>
                      {{-- 右側 --}}
                      <!-- 三点リーダー（モーダル） -->
                      <div class="flex items-end sm:justify-end">
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
          </section>
        </div>

      </div>
    </div>
  </div>

</x-app-layout>