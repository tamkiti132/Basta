<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
      グループ編集
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">

        <x-flash-message />

        {{-- グループ情報更新 --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <form class="p-4" method="POST"
                action="{{ route('group.group_edit.update', ['id' => $group_data['id']]) }}"
                enctype='multipart/form-data'>
                @csrf
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  <div class="grid sm:grid-cols-5">
                    {{-- 左側 --}}
                    <div class="sm:col-span-3">
                      {{-- photo --}}
                      <div class="flex content-center">
                        @if($group_data->group_photo_path)
                        <div class="object-cover mr-3 bg-center rounded-full h-14 sm:h-20 w-14 sm:w-20">
                          <img class="object-fill rounded-full sm:w-20 h-14 sm:h-20 w-14"
                            src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                        </div>
                        @else
                        <div class="object-cover mr-3 bg-blue-200 bg-center rounded-full h-14 sm:h-20 w-14 sm:w-20">
                        </div>
                        @endif
                        {{-- end_photo --}}
                      </div>

                      {{-- 画像設定ボタン --}}
                      <x-validation-error name="group_image" />
                      <div class="flex gap-4 mt-3 leading-none y-4">
                        <div>
                          <label for="group_image"
                            class="px-2 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50">画像を選択</label>
                          <input id="group_image" class="hidden" type="file" name="group_image"></input>
                        </div>
                        <div>
                          <label for="delete_photo"
                            class="px-6 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50">画像を削除</label>
                          <input id="delete_photo" class="hidden" type="file"></input>
                        </div>
                      </div>

                      {{-- グループ情報入力 --}}
                      <div class="mt-5">
                        <x-validation-error name="group_name" />
                        <label for="group_name" class="block text-sm">グループ名<span class="required">*</span></label>
                        <input id="group_name" type="text" class="rounded-lg" name="group_name" size="30"
                          value="{{ old('group_name', $group_data->name) }}">
                      </div>

                      <div class="mt-5">
                        <x-validation-error name="introduction" />
                        <label for="introduction" class="block text-sm">グループ紹介文<span class="required">*</span></label>
                        <textarea id="introduction" type="text" name="introduction" class="w-full rounded-lg"
                          rows="6">{{ old('introduction', $group_data->introduction) }}</textarea>
                      </div>

                    </div>
                    {{-- 右側 --}}
                    {{-- ボタン --}}
                    <div class="flex items-center justify-center pt-10 sm:col-span-2 sm:justify-end sm:pt-0">
                      <button
                        class="py-3 text-lg font-bold text-white bg-indigo-400 border-0 px-14 rounded-2xl focus:outline-none hover:bg-indigo-500"
                        type="">更新する</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </section>

        {{-- メンバー編集 --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  <div class="items-center sm:grid sm:grid-cols-5">
                    {{-- 左側 --}}
                    <div class="text-center sm:text-left sm:col-span-3">
                      <p>メンバー編集</p>
                    </div>
                    {{-- 右側 --}}
                    {{-- ボタン --}}
                    <div class="flex items-center justify-center pt-10 sm:justify-end sm:col-span-2 sm:pt-0">
                      <button
                        class="py-3 text-lg font-bold text-white bg-indigo-400 border-0 px-14 rounded-2xl focus:outline-none hover:bg-indigo-500"
                        onclick="location.href='{{ route('group.member_edit.index') }}' ">編集する</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {{-- グループの自由参加 --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  <div class="flex items-center justify-between">
                    {{-- 左側 --}}
                    <div>
                      <p>グループの自由参加</p>
                    </div>
                    {{-- 右側 --}}
                    {{-- ボタン --}}
                    @livewire('toggle-join-free-enable-button', ['groupId' => $group_data['id']])
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {{-- グループに招待する --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  <form method="GET" action="{{ route('group.group_edit.sendMail') }}"
                    class="items-center sm:grid sm:grid-cols-5">
                    {{-- 左側 --}}
                    <div class="flex justify-center sm:justify-start sm:text-left sm:col-span-3">
                      <div>
                        <label for="email" class="block text-sm">グループに招待する<span class="required">*</span></label>
                        <input id="email" type="text" name="email" class="rounded-lg" placeholder="Eメールアドレス" size="30">
                        <input type="hidden" name="group_name" value="{{ $group_data->name }}">
                      </div>
                    </div>
                    {{-- 右側 --}}
                    {{-- ボタン --}}
                    <div class="flex items-center justify-center pt-10 sm:justify-end sm:col-span-2 sm:pt-0">
                      <button
                        class="py-3 text-lg font-bold text-white bg-indigo-400 border-0 px-14 rounded-2xl focus:outline-none hover:bg-indigo-500"
                        type="submit">招待する</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>

        {{-- グループ内投げ銭 --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  <div class="flex items-center justify-between">
                    {{-- 左側 --}}
                    <div>
                      <p>グループ内投げ銭 <br>
                        （100円 / 回）</p>
                    </div>
                    {{-- 右側 --}}
                    {{-- ボタン --}}
                    @livewire('toggle-tip-enable-button', ['groupId' => $group_data['id']])
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
      </section>

      {{-- グループを削除 --}}
      <section class="text-gray-600 body-font">
        <div class="container px-5 mx-auto">
          <div class="-m-4 ">
            <div class="p-4">
              <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                <div class="items-center sm:grid sm:grid-cols-5">
                  {{-- 左側 --}}
                  <div class="text-center sm:col-span-3 sm:text-left">
                    <p>グループを削除</p>
                  </div>
                  {{-- 右側 --}}
                  {{-- ボタン --}}
                  <div class="flex items-center justify-center pt-10 sm:justify-end sm:col-span-2 sm:pt-0">
                    <button
                      class="px-8 py-3 text-lg font-bold text-red-700 border border-red-700 rounded-2xl focus:outline-none hover:bg-red-100"
                      onclick="Livewire.emit('showModal')">グループを削除</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {{-- グループ削除確認モーダル --}}
      @livewire('delete-group-form')

    </div>
  </div>


</x-app-layout>