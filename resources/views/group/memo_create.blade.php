<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
      メモ投稿
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
        <x-validation-errors class="mb-4" />
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="flex flex-wrap justify-center -m-4">
              <div class="p-4">
                <div
                  class="relative px-4 pt-8 pb-16 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                  <div class="mb-10 border-b border-gray-400">
                    {{-- Web / 本 切り替え --}}
                    <div class="flex lg:w-1/2">
                      <button
                        class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                        type="button" x-on:click="form_web = true; form_book = false"
                        x-bind:class="form_web ? 'border-b-4 border-blue-300' :'' ">
                        <i class="text-3xl fas fa-globe"></i>
                      </button>
                      <button
                        class="w-1/2 text-center transition duration-700 ease-in-out rounded-t-xl hover:bg-blue-100"
                        type="button" x-on:click="form_web = false; form_book = true"
                        x-bind:class="form_book ? 'border-b-4 border-blue-300' :'' ">
                        <i class="text-3xl fas fa-book-open"></i>
                      </button>
                    </div>
                  </div>
                  <form method="POST" action="{{ route('group.memo_create.store') }}" enctype='multipart/form-data'>
                    @csrf
                    {{-- Web の 場合 --}}
                    <template x-if="form_web">
                      <div>
                        <div class="grid xl:grid-cols-7">
                          {{-- 左側 --}}
                          <div class="xl:col-span-3">
                            {{-- Webタイプのメモであることを示すデータ --}}
                            <input type="hidden" name="memo_type" value="web">
                            {{-- メモ情報 --}}
                            <div>
                              <label for="web_title" class="block text-sm">タイトル<span class="required">*</span></label>
                              <input id="web_title" type="text" name="web_title" value="{{ old('web_title') }}"
                                class="w-full mb-3 text-sm rounded-lg sm:w-3/4 sm:text-base">
                            </div>
                            <div class="mt-3">
                              <label for="url" class="block text-sm">URL<span class="required">*</span></label>
                              <input id="url" type="url" name="url" value="{{ old('url') }}"
                                class="w-full text-sm rounded-lg sm:w-3/4 sm:text-base">
                            </div>

                            {{-- タグ --}}
                            @livewire('label-attached-to-new-memo')

                          </div>
                          {{-- 右側 --}}
                          <div class="mt-6 xl:col-span-3 xl:mt-0">
                            <div>
                              <label for="web_shortMemo" class="block text-sm">ひとことメモ<span
                                  class="required">*</span></label>
                              <textarea id="web_shortMemo" name="web_shortMemo" type="text"
                                class="w-full text-sm rounded-lg sm:text-base" cols="60"
                                rows="6">{{ old('web_shortMemo') }}</textarea>
                            </div>
                            <div class="mt-3">
                              <label for="select_label"
                                class="px-6 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50"
                                x-on:click="modal_label_select = true">ラベルを追加</label>
                              <input id="select_label" class="hidden"></input>
                            </div>
                          </div>
                        </div>
                        {{-- 自由記入欄 --}}
                        <div class="mt-10">
                          <label for="web_additionalMemo" class="block text-sm">自由記入欄</label>
                          <textarea id="web_additionalMemo" type="text" name="web_additionalMemo"
                            class="w-full text-sm rounded-lg sm:text-base"
                            rows="10">{{ old('web_additionalMemo') }}</textarea>
                        </div>

                        {{-- ボタン --}}
                        <div class="mt-10 text-center">
                          <button
                            class="px-10 py-3 text-base font-bold text-white bg-indigo-400 border-0 sm:px-24 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500"
                            type="submit">メモ投稿</button>
                        </div>
                      </div>
                    </template>
                    {{-- 本 の 場合 --}}
                    <template x-if="form_book">
                      <div>
                        <div class="grid xl:grid-cols-7">
                          {{-- 本タイプのメモであることを示すデータ --}}
                          <input type="hidden" name="memo_type" value="book">

                          {{-- 左側 --}}
                          <div class="xl:col-span-3">

                            {{-- メモ情報 --}}
                            <div>
                              <label for="book_title" class="block text-sm">タイトル<span class="required">*</span></label>
                              <input id="book_title" type="text" name="book_title" value="{{ old('book_title') }}"
                                class="w-full mb-3 text-sm rounded-lg sm:w-3/4 sm:text-base">
                            </div>
                            <div class="sm:mt-3">

                            </div>
                          </div>
                          {{-- 真ん中 --}}
                          <div class="mt-6 xl:col-span-3 xl:mt-0">
                            <div>
                              <label for="book_shortMemo" class="block text-sm">ひとことメモ<span
                                  class="required">*</span></label>
                              <textarea id="book_shortMemo" type="text" name="book_shortMemo"
                                class="w-full text-sm rounded-lg sm:text-base" cols="60"
                                rows="6">{{ old('book_shortMemo') }}</textarea>
                            </div>
                            <div class="mt-3">
                              <label for="select_label"
                                class="px-6 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50"
                                x-on:click="modal_label_select = true">ラベルを追加</label>
                              <input id="select_label" class="hidden"></input>
                            </div>
                          </div>
                          {{-- 右側 --}}
                          <div class="grid grid-cols-5">
                            {{-- <div class="col-span-2">
                            </div> --}}
                            <div class="col-span-5">
                              <div class="max-w-xs m-auto">
                                <img src="/images/本の画像（青）.png">
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
                          <label for="book_additionalMemo" class="block text-sm">自由記入欄</label>
                          <textarea id="book_additionalMemo" type="text" class="w-full text-sm rounded-lg sm:text-base"
                            name="book_additionalMemo" rows="10">{{ old('book_additionalMemo') }}</textarea>
                        </div>

                        {{-- ボタン --}}
                        <div class="mt-10 text-center ">
                          <button
                            class="px-10 py-3 text-base font-bold text-white bg-indigo-400 border-0 sm:px-24 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500"
                            type="submit">メモ投稿</button>
                        </div>
                      </div>
                    </template>
                  </form>


                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>

  {{-- ラベル追加モーダル --}}
  <div x-cloak x-show="modal_label_select"
    class="fixed top-0 left-0 z-30 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
    <div x-on:click.away="modal_label_select = false"
      class="flex flex-col w-full h-auto max-w-xs px-3 py-2 bg-white rounded-xl">

      @livewire('label-adder')

      {{-- <form action="" method="POST" class="flex flex-col gap-2 p-2 text-xs sm:text-base">
        <div>
          <input type="checkbox" name="reason" value="0" class="mr-3"><span>プログラミング</span>
        </div>
        <div>
          <input type="checkbox" name="reason" value="1" class="mr-3"><span>設計</span>
        </div>
        <div>
          <input type="checkbox" name="reason" value="2" class="mr-3"><span>Laravel</span>
        </div>
      </form> --}}

    </div>
  </div>



</x-app-layout>