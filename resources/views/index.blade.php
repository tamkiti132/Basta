<x-app-layout>
  <x-slot name="header">
    <div class="grid grid-cols-2">
      {{-- 左側 --}}
      <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
        参加グループ一覧
      </h2>

      {{-- 右側 --}}
      <div class="flex justify-end">
        <div class="flex-wrap gap-4 sm:flex sm:gap-10 sm:flex-nowrap">
          <div class="sm:w-auto">
            <a class="text-sm font-semibold leading-tight text-gray-800 sm:text-xl"
              href="{{ route('group_create.create') }}">
              グループをつくる
            </a>
          </div>
          <div class="sm:w-auto">
            <a class="text-sm font-semibold leading-tight text-gray-800 sm:text-xl"
              href="{{ route('group_join.index') }}">
              グループに参加する
            </a>
          </div>

        </div>
      </div>
    </div>
  </x-slot>

  <div class="pt-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <form method="get" action="{{ route('index') }}" class="text-left">
      <input type="text" name="search" placeholder="グループ名か紹介文のワードで検索" class="rounded-xl" size="50">
      <button class="px-3 py-2 font-bold">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
    </form>
  </div>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="grid gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">

        <x-flash-message status="suspension" />

        {{-- @php
        dd($my_groups_data)
        @endphp --}}
        @foreach ($my_groups_data as $group_data)
        <section class="w-full text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="flex flex-wrap justify-center -m-4">
              <div class="w-full p-4">
                <div class="relative px-8 py-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  <div class="grid gap-10 sm:grid-cols-2 sm:gap-0">
                    {{-- 左側 --}}
                    <div>
                      <div class="flex items-start content-center">
                        {{-- photo --}}
                        @if($group_data->group_photo_path)
                        <div class="flex-shrink-0 object-cover mr-3 bg-center rounded-full h-14 w-14">
                          <img class="object-fill rounded-full h-14 w-14"
                            src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                        </div>
                        @else
                        <div class="flex-shrink-0 object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 w-14"></div>
                        @endif
                        {{-- end_photo --}}
                        <h1 class="self-center text-xl font-bold text-gray-700 title-font sm:text-2xl">{{
                          $group_data->name }}
                        </h1>
                      </div>
                      <div class="mt-2 leading-none y-4 ">
                        <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                          {{-- @php
                          dd($group_data->userRoles)
                          @endphp --}}
                          管理者：{{ $group_data->userRoles->first()->nickname }}
                        </p>
                        <p class="items-center pt-5 ml-3 text-sm leading-none text-gray-700">
                          メンバー：{{ $group_data->user_count }}人
                        </p>
                      </div>
                    </div>
                    {{-- 右側 --}}
                    <div>
                      <p class="mb-3 leading-relaxed">{!! nl2br(e($group_data->introduction)) !!}</p>
                    </div>
                  </div>

                  {{-- ボタン --}}
                  <div class="px-10 pt-10 text-center">
                    <button
                      class="w-2/3 px-10 py-3 text-lg font-bold text-white bg-indigo-400 border-0 lg:w-1/3 rounded-2xl focus:outline-none hover:bg-indigo-500"
                      onclick="location.href='{{ route('group.index', ['group_id' => $group_data['id']]) }}' ">入室</button>
                    {{-- <button
                      class="px-10 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                      onclick="location.href='{{ route('group.group_edit.edit', ['group_id' => $group_data['id']]) }}' ">編集</button>
                    --}}

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

  <div class="flex justify-center">
    {{ $my_groups_data->links() }}
  </div>


</x-app-layout>