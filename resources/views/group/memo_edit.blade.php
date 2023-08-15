<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
      メモ編集
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
        {{-- @php
        dd($memo_data);
        @endphp --}}
        @if ($memo_data->type == 0)
        {{-- Webタイプの　場合 --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="flex flex-wrap justify-center -m-4">
              <form class="p-4" method="POST" action="{{ route('group.memo_edit.update', ['id' => $memo_data['id']]) }}"
                enctype='multipart/form-data'>
                @csrf
                <div
                  class="relative px-4 pt-8 pb-16 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                  <div class="grid xl:grid-cols-7">
                    {{-- 左側 --}}
                    <div class="xl:col-span-3">
                      {{-- Webタイプのメモであることを示すデータ --}}
                      <input type="hidden" name="memo_type" value="web">
                      {{-- メモ情報 --}}
                      <div>
                        <x-validation-error name="web_title" />
                        <label for="web_title" class="block text-sm">タイトル<span class="required">*</span></label>
                        <input id="web_title" type="text" name="web_title"
                          class="w-full mb-3 text-sm rounded-lg sm:w-3/4 sm:text-base"
                          value="{{ old('web_title', $memo_data->title) }}">
                      </div>
                      <div class="mt-3">
                        <x-validation-error name="url" />
                        <label for="url" class="block text-sm">URL<span class="required">*</span></label>
                        <input id="url" type="text" name="url" class="w-full text-sm rounded-lg sm:w-3/4 sm:text-base"
                          value="{{ old('url', $memo_data->web_type_feature->url) }}">
                      </div>
                      {{-- タグ --}}
                      @livewire('label-attached-to-memo-list', ['memoId' => $memo_data->id ])
                    </div>
                    {{-- 真ん中 --}}
                    <div class="mt-6 xl:col-span-3 xl:mt-0">
                      <div>
                        <x-validation-error name="web_shortMemo" />
                        <label for="web_shortMemo" class="block text-sm">ひとことメモ<span class="required">*</span></label>
                        <textarea id="web_shortMemo" name="web_shortMemo" type="text"
                          class="w-full text-sm rounded-lg sm:text-base" cols="60"
                          rows="6">{{ old('web_shortMemo', $memo_data->shortMemo) }}</textarea>
                      </div>
                      <div class="mt-3">
                        <label for="select_label"
                          class="px-6 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50"
                          x-on:click="modal_label_select = true">ラベルを選択</label>
                        <input id="select_label" class="hidden"></input>
                      </div>
                    </div>
                    <div class="hidden text-right xl:block">
                      <i class="text-3xl fas fa-globe"></i>
                    </div>
                  </div>
                  <div class="mt-10">
                    <x-validation-error name="web_additionalMemo" />
                    <label for="web_additionalMemo" class="block text-sm">自由記入欄</label>
                    <textarea id="web_additionalMemo" type="text" name="web_additionalMemo"
                      class="w-full text-sm rounded-lg sm:text-base"
                      rows="10">{{ old('web_additionalMemo', $memo_data->additionalMemo) }}</textarea>
                  </div>
                  {{-- ボタン --}}
                  <div class="mt-10 text-center">
                    <button
                      class="px-10 py-3 text-base font-bold text-white bg-indigo-400 border-0 sm:px-24 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500"
                      type="">メモ更新</button>
                  </div>


                </div>
              </form>
            </div>
          </div>
        </section>
        @elseif ($memo_data->type == 1)
        {{-- 本タイプ　の場合 --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="flex flex-wrap justify-center -m-4">
              <form class="p-4" method="POST" action="{{ route('group.memo_edit.update', ['id' => $memo_data['id']]) }}"
                enctype='multipart/form-data'>
                @csrf
                <div
                  class="relative px-4 pt-8 pb-16 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                  <div class="grid xl:grid-cols-7">
                    {{-- 左側 --}}
                    {{-- Bookタイプのメモであることを示すデータ --}}
                    <input type="hidden" name="memo_type" value="book">
                    <div class="xl:col-span-3">
                      {{-- メモ情報 --}}
                      <div>
                        <x-validation-error name="book_title" />
                        <label for="book_title" class="block text-sm">タイトル<span class="required">*</span></label>
                        <input id="book_title" type="text" name="book_title"
                          class="w-full mb-3 text-sm rounded-lg sm:w-3/4 sm:text-base"
                          value="{{ old('book_title', $memo_data->title) }}">
                      </div>
                      {{-- タグ --}}
                      <div class="mt-12 sm:mt-32">
                        @livewire('label-attached-to-memo-list', ['memoId' => $memo_data->id ])
                      </div>
                    </div>
                    {{-- 真ん中 --}}
                    <div class="mt-6 xl:col-span-3 xl:mt-0">
                      <div>
                        <x-validation-error name="book_shortMemo" />
                        <label for="book_shortMemo" class="block text-sm">ひとことメモ<span class="required">*</span></label>
                        <textarea id="book_shortMemo" type="text" name="book_shortMemo"
                          class="w-full text-sm rounded-lg sm:text-base" cols="60"
                          rows="6">{{ old('book_shortMemo', $memo_data->shortMemo) }}</textarea>
                      </div>
                      <div class="mt-3">
                        <label for="select_label"
                          class="px-6 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50"
                          x-on:click="modal_label_select = true">ラベルを選択</label>
                        <input id="select_label" class="hidden"></input>
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
                          @if(!empty($memo_data->book_type_feature->book_photo_path))
                          <img
                            src="{{ asset('storage/book-image/'. $memo_data->book_type_feature->book_photo_path) }}" />
                          @endif
                        </div>
                        <div class="mt-3">
                          <!-- TODO: バリデーションでエラーが起きた際の画像の復元は難しい問題なのであとでやる -->
                          <label for="book_image"
                            class="px-2 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50">画像を選択</label>
                          <input id="book_image" class="hidden" type="file" name="book_image"></input>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="mt-10">
                    <x-validation-error name="book_additionalMemo" />
                    <label for="book_additionalMemo" class="block text-sm">自由記入欄</label>
                    <textarea id="book_additionalMemo" type="text" name="book_additionalMemo"
                      class="w-full text-sm rounded-lg sm:text-base"
                      rows="10">{{ old('book_additionalMemo', $memo_data->additionalMemo) }}</textarea>
                  </div>
                  {{-- ボタン --}}
                  <div class="mt-10 text-center">
                    <button
                      class="px-10 py-3 text-base font-bold text-white bg-indigo-400 border-0 sm:px-24 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500"
                      type="">メモ更新</button>
                  </div>


                </div>
            </div>
          </div>
      </div>
      </section>

      @endif
    </div>
  </div>
  </div>

  {{-- ラベル選択モーダル --}}
  <div x-cloak x-show="modal_label_select"
    class="fixed top-0 left-0 z-30 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
    <div x-on:click.away="modal_label_select = false"
      class="flex flex-col w-full h-auto max-w-xs px-3 py-2 bg-white rounded-xl">

      @livewire('label-selector', ['memoId' => $memo_data->id ])

    </div>
  </div>

</x-app-layout>