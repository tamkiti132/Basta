<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
      メンバー一覧
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">

        <section class="text-gray-600 body-font"
          x-data="{modal_leave_group: false, currentUserId: null, actionUrl: ''}">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="grid gap-10 px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md sm:gap-7 rounded-2xl">
                  {{-- メンバー / ブロック中のメンバー --}}
                  <div class="border-b border-gray-400">
                    <div class="flex text-xs font-bold sm:text-lg sm:w-1/2">
                      <button
                        class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                        type="button" x-on:click="member = true; block_member= false"
                        x-bind:class="member ? 'border-b-4 border-blue-300' :'' ">
                        <p>メンバー</p>
                      </button>
                      <button
                        class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                        type="button" x-on:click="member = false; block_member= true"
                        x-bind:class="block_member ? 'border-b-4 border-blue-300' :'' ">
                        <p>ブロック中のメンバー</p>
                      </button>
                    </div>
                  </div>
                  {{-- メンバー を選択した 場合 --}}
                  @foreach ($all_not_blocked_users_data as $user_data)
                  {{-- １人分のまとまり --}}
                  <div class="grid gap-10 sm:gap-7" x-cloak x-show="member">
                    <div class="items-center sm:grid sm:grid-cols-7">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-2">
                        @if($user_data->profile_photo_path)
                        <button class="object-cover w-10 h-10 mr-3 bg-center rounded-full"
                          onclick="location.href='{{ route('group.member.show', ['id' => $user_data->id]) }}' ">
                          <img class="object-fill w-10 h-10 rounded-full"
                            src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                        </button>
                        @else
                        <button class="object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full"
                          onclick="location.href='{{ route('group.member.show', ['id' => $user_data->id]) }}' "></button>
                        @endif
                        <p>{{ $user_data->nickname }}</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="ml-16 sm:ml-0 sm:col-span-2">
                        <button class="text-sm text-gray-500"
                          onclick="location.href='{{-- route('group.member') --}}' ">
                          {{ $user_data->username }}
                        </button>
                      </div>
                      {{-- 投稿数 ・ 権限 ・ 三点リーダー（モーダル） --}}
                      <div class="grid items-center grid-cols-3 text-center sm:text-left sm:col-span-3">
                        {{-- 投稿数 --}}
                        <div class="mt-3 sm:mt-0">
                          <p class="text-xs sm:text-sm">{{ $user_data->memo_count }}<span class="ml-3">投稿</span></p>
                        </div>
                        {{-- 権限 --}}

                        @php
                        $role = $user_data->groupRoles->first()->pivot->role;
                        @endphp

                        @if(auth()->id() != $user_data->id)
                        <form method="POST"
                          action="{{ route('group.member_edit.updateRole', ['user' => $user_data->id]) }}">
                          @csrf
                          <div class="mt-3 sm:mt-0">
                            <select name="role" class="pl-0 text-xs bg-transparent border-none sm:text-base"
                              onchange="this.form.submit()">
                              <option value="50" {{ ($role==50) ? 'selected' : '' }}>サブ管理者</option>
                              <option value="100" {{ ($role==100) ? 'selected' : '' }}>メンバー</option>
                            </select>
                          </div>
                        </form>
                        @else
                        @if ($role == 10)
                        管理者
                        @elseif ($role == 50)
                        サブ管理者
                        @elseif ($role == 100)
                        メンバー
                        @endif
                        @endif
                        <!-- 三点リーダー（モーダル） -->
                        @if(auth()->id() != $user_data->id)
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
                                <button class="block p-2 text-left hover:bg-slate-100" x-on:click="
                                              modal_leave_group = true; 
                                              currentUserId = {{ $user_data->id }}; 
                                              let url = '{{ route('group.member_edit.quit', ['group_id' => $group_data->id, 'user_id' => '__userId__']) }}';
                                              actionUrl = url.replace('__userId__', currentUserId);">退会させる</button>
                                <button class="block p-2 text-left hover:bg-slate-100"
                                  onclick="location.href='{{ route('group.member_edit.blockMember', ['id' => $user_data->id]) }}' ">ブロックする</button>
                              </div>
                            </x-slot>
                          </x-dropdown>
                        </div>
                        @endif

                      </div>

                      {{-- 退会確認モーダル --}}
                      <div x-cloak x-show="modal_leave_group"
                        class="fixed top-0 left-0 z-30 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
                        <form :action="actionUrl" method="POST" x-on:click.away="modal_leave_group = false"
                          class="flex flex-col justify-center w-full h-auto max-w-sm px-3 py-2 bg-white rounded-xl">
                          @csrf

                          <div class="flex flex-col items-center pb-2 mb-6">
                            @if($group_data->group_photo_path)
                            <div class="object-cover w-8 h-8 mr-3 bg-center rounded-full">
                              <img class="object-fill w-8 h-8 rounded-full"
                                src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                            </div>
                            @else
                            <div class="object-cover w-8 h-8 bg-blue-200 rounded-full"></div>
                            @endif
                            <p>{{ $group_data->name }}</p>
                          </div>

                          <div class="flex justify-center mb-6 text-sm font-bold text-center">
                            <p class="leading-relaxed">本当に退会させますか？<br>
                              （グループ内で投稿したメモ、コメントは残ります）</p>
                          </div>

                          <div action="" method="GET" class="flex flex-col gap-2 p-2">

                            <label for="password">パスワード</label>
                            <input type="password" id="password" name="password"
                              class="text-sm rounded-lg sm:text-base">

                            <div class="flex justify-end gap-4 pt-2">
                              <button class="px-1 py-2 border border-gray-300 w-28 hover:bg-slate-100" type="button"
                                x-on:click="modal_leave_group = false">キャンセル</button>
                              <button type="submit"
                                class="px-1 py-2 text-red-500 border border-red-500 w-28 hover:bg-red-50"
                                x-on:click="modal_select_next_manager = true; modal_leave_group = false">退会</button>
                            </div>
                          </div>

                        </form>
                      </div>

                    </div>
                  </div>
                  @endforeach

                  {{-- ブロック中のメンバー を選択した場合 --}}
                  @foreach ($all_blocked_users_data as $user_data)
                  <div class="grid gap-10 sm:gap-7" x-cloak x-show="block_member">
                    {{-- １人分のまとまり --}}
                    <div class="items-center sm:grid sm:grid-cols-7">
                      {{-- プロフィール画像 ・ ニックネーム --}}
                      <div class="flex items-center sm:col-span-2">
                        @if($user_data->profile_photo_path)
                        <button class="object-cover w-10 h-10 mr-3 bg-center rounded-full"
                          onclick="location.href='{{ route('group.member.show', ['id' => $user_data->id]) }}' ">
                          <img class="object-fill w-10 h-10 rounded-full"
                            src="{{ asset('storage/'. $user_data->profile_photo_path) }}" />
                        </button>
                        @else
                        <button class="object-cover w-10 h-10 mr-3 bg-blue-200 bg-center rounded-full"
                          onclick="location.href='{{ route('group.member.show', ['id' => $user_data->id]) }}' "></button>
                        @endif
                        <p>{{ $user_data->nickname }}</p>
                      </div>
                      {{-- ユーザーid --}}
                      <div class="ml-16 sm:mt-0 sm:ml-0 sm:col-span-2">
                        <p class="text-sm text-gray-500">
                          {{ $user_data->username }}
                        </p>
                      </div>
                      {{-- 投稿数 ・ 権限 ・ 三点リーダー（モーダル） --}}
                      <div class="grid items-center grid-cols-3 text-center sm:text-left sm:col-span-3">
                        {{-- 投稿数 --}}
                        <div class="mt-3 sm:mt-0">
                          <p class="text-xs sm:text-sm">{{ $user_data->memo_count }}<span class="ml-3">投稿</span></p>
                        </div>
                        {{-- 権限 --}}
                        <div class="mt-3 sm:mt-0">
                          <p class="text-xs sm:text-base">
                            @php
                            $role = $user_data->groupRoles->first()->pivot->role;
                            @endphp

                            @if(auth()->id() != $user_data->id)
                          <form method="POST"
                            action="{{ route('group.member_edit.updateRole', ['user' => $user_data->id]) }}">
                            @csrf
                            <div class="mt-3 sm:mt-0">
                              <select name="role"
                                class="pl-0 text-xs bg-transparent border-none form-control sm:text-base"
                                onchange="this.form.submit()">
                                <option value="50" {{ ($role==50) ? 'selected' : '' }}>サブ管理者</option>
                                <option value="100" {{ ($role==100) ? 'selected' : '' }}>メンバー</option>
                              </select>
                            </div>
                          </form>
                          @else
                          @if ($role == 10)
                          管理者
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
                                <button class="block p-2 text-left hover:bg-slate-100" x-on:click="modal_leave_group = true; 
                                              currentUserId = {{ $user_data->id }}; 
                                              let url = '{{ route('group.member_edit.quit', ['group_id' => $group_data->id, 'user_id' => '__userId__']) }}';
                                              actionUrl = url.replace('__userId__', currentUserId);">退会させる</button>
                                <button class="block p-2 text-left hover:bg-slate-100"
                                  onclick="location.href='{{ route('group.member_edit.liftBlockMember', ['id' => $user_data->id]) }}' ">ブロック解除する</button>
                              </div>
                            </x-slot>
                          </x-dropdown>
                        </div>
                        @endif

                      </div>

                      {{-- 退会確認モーダル --}}
                      <div x-cloak x-show="modal_leave_group"
                        class="fixed top-0 left-0 z-30 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
                        <form :action="actionUrl" method="POST" x-on:click.away="modal_leave_group = false"
                          class="flex flex-col justify-center w-full h-auto max-w-sm px-3 py-2 bg-white rounded-xl">
                          @csrf

                          <div class="flex flex-col items-center pb-2 mb-6">
                            @if($group_data->group_photo_path)
                            <div class="object-cover w-8 h-8 mr-3 bg-center rounded-full">
                              <img class="object-fill w-8 h-8 rounded-full"
                                src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                            </div>
                            @else
                            <div class="object-cover w-8 h-8 bg-blue-200 rounded-full"></div>
                            @endif
                            <p>{{ $group_data->name }}</p>
                          </div>

                          <div class="flex justify-center mb-6 text-sm font-bold text-center">
                            <p class="leading-relaxed">本当に退会しますか？<br>
                              （グループ内で投稿したメモ、コメントは残ります）</p>
                          </div>

                          <div action="" method="GET" class="flex flex-col gap-2 p-2">

                            <label for="password">パスワード</label>
                            <input type="password" id="password" name="password"
                              class="text-sm rounded-lg sm:text-base">

                            <div class="flex justify-end gap-4 pt-2">
                              <button class="px-1 py-2 border border-gray-300 w-28 hover:bg-slate-100" type="button"
                                x-on:click="modal_leave_group = false">キャンセル</button>
                              <button type="submit"
                                class="px-1 py-2 text-red-500 border border-red-500 w-28 hover:bg-red-50"
                                x-on:click="modal_select_next_manager = true; modal_leave_group = false">退会</button>
                            </div>
                          </div>

                        </form>
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
  </div>

  {{-- メンバー の ページネーション --}}
  <div class="flex justify-center" x-cloak x-show="member">
    {{ $all_not_blocked_users_data->withQueryString()->links() }}
  </div>

  {{-- ブロック中メンバー の ページネーション --}}
  <div class="flex justify-center" x-cloak x-show="block_member">
    {{ $all_blocked_users_data->withQueryString()->links() }}
  </div>


</x-app-layout>