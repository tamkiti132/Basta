<div x-data="{
    form_web: true,
    form_book: false,
    modal_label_select: false,
}">

    {{-- このCSSの記述を書いた理由は、『本』のタブを選択した際（form_bookをtrueにした際）、
        『本 の 場合 』の中のtextareaの高さが狭くなってしまう不具合の応急処置をするため --}}
    <style>
        #book_shortMemo {
            min-height: 160px; /* 開始時の最小高さ */
        }

        #book_additionalMemo{
            min-height: 256px; /* 開始時の最小高さ */
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            メモ投稿
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl text-sm sm:px-6 lg:px-8">

            <div class="grid overflow-hidden gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">

                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div
                                    class="overflow-hidden relative px-4 pt-8 pb-16 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md sm:px-8">
                                    <div class="mb-10 border-b border-gray-400">
                                        {{-- Web / 本 切り替え --}}
                                        <div class="flex lg:w-1/2">
                                            <button
                                                class="w-1/2 text-center rounded-t-xl transition duration-700 ease-in-out hover:bg-blue-100"
                                                type="button" x-on:click="form_web = true; form_book = false"
                                                x-bind:class="form_web ? 'border-b-4 border-blue-300' :'' ">
                                                <i class="text-xl fas fa-globe"></i>
                                            </button>
                                            <button
                                                class="w-1/2 text-center rounded-t-xl transition duration-700 ease-in-out hover:bg-blue-100"
                                                type="button" x-on:click="form_web = false; form_book = true"
                                                x-bind:class="form_book ? 'border-b-4 border-blue-300' :'' ">
                                                <i class="text-xl fas fa-book-open"></i>
                                            </button>
                                        </div>
                                    </div>
                                    {{-- Web の 場合 --}}
                                    <div x-show="form_web" x-cloak>
                                        <form wire:submit.prevent="store">
                                            @csrf
                                            <div class="grid lg:grid-cols-7">
                                                {{-- 左側 --}}
                                                <div class="lg:col-span-3">
                                                    {{-- Webタイプのメモであることを示すデータ --}}
                                                    <input type="hidden" wire:model.defer="type" value="web">
                                                    {{-- メモ情報 --}}
                                                    <div>
                                                        <div>
                                                            @error('web_title')
                                                            <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                            @enderror
                                                        </div>
                                                        <label for="web_title" class="block">タイトル<span
                                                                class="required">*</span></label>
                                                        <input id="web_title" type="text" wire:model.defer="web_title"
                                                            class="mb-3 w-full rounded-lg lg:w-3/4">
                                                    </div>
                                                    <div class="mt-3">
                                                        <div>
                                                            @error('url')
                                                            <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                            @enderror
                                                        </div>
                                                        <label for="url" class="block">URL<span
                                                                class="required">*</span></label>
                                                        <input id="url" type="url" wire:model.defer="url"
                                                            class="w-full rounded-lg lg:w-3/4">
                                                    </div>

                                                    {{-- ラベル追加ボタン　（スマホ　・　タブレット　用の表示） --}}
                                                    <div class="block mt-3 lg:hidden">
                                                        <label for="select_label"
                                                            class="px-6 py-1 text-xs font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50"
                                                            x-on:click="modal_label_select = true">ラベルを追加</label>
                                                        <input id="select_label" class="hidden"></input>
                                                    </div>

                                                    {{-- タグ --}}
                                                    @livewire('label-attached')


                                                </div>
                                                {{-- 右側 --}}
                                                <div class="mt-6 lg:col-span-3 lg:mt-0">
                                                    <div>
                                                        <div>
                                                            @error('web_shortMemo')
                                                            <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                            @enderror
                                                        </div>
                                                        <label for="web_shortMemo" class="block">ひとことメモ<span
                                                                class="required">*</span></label>
                                                        <textarea id="web_shortMemo" type="text"
                                                            wire:model.defer="web_shortMemo" class="w-full rounded-lg"
                                                            cols="60" rows="6"></textarea>
                                                    </div>

                                                    {{-- ラベル追加ボタン　（PC用　の表示） --}}
                                                    <div class="hidden mt-3 lg:block">
                                                        <label for="select_label"
                                                            class="px-6 py-1 text-xs font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50"
                                                            x-on:click="modal_label_select = true">ラベルを追加</label>
                                                        <input id="select_label" class="hidden"></input>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- 自由記入欄 --}}
                                            <div class="mt-10">
                                                <div>
                                                    @error('web_additionalMemo')
                                                    <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                    @enderror
                                                </div>
                                                <label for="web_additionalMemo" class="block">自由記入欄</label>
                                                <textarea id="web_additionalMemo" type="text"
                                                    wire:model.defer="web_additionalMemo" class="w-full rounded-lg"
                                                    rows="10"></textarea>
                                            </div>

                                            {{-- ボタン --}}
                                            <div class="mt-10 text-center">
                                                <button
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 sm:px-24 focus:outline-none hover:bg-indigo-500"
                                                    wire:click="store('web')" type="button">メモ投稿</button>
                                            </div>

                                        </form>
                                    </div>
                                    {{-- 本 の 場合 --}}
                                    <div x-show="form_book" x-cloak>
                                        <form wire:submit.prevent="store">
                                            @csrf
                                            <div class="grid lg:grid-cols-7">
                                                {{-- 本タイプのメモであることを示すデータ --}}
                                                <input type="hidden" wire:model.defer="type" value="book">

                                                {{-- 左側 --}}
                                                <div class="lg:col-span-3">

                                                    {{-- メモ情報 --}}
                                                    <div>
                                                        @error('book_title')
                                                        <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                        @enderror
                                                        <label for="book_title" class="block">タイトル<span
                                                                class="required">*</span></label>
                                                        <input id="book_title" type="text" wire:model.defer="book_title"
                                                            class="mb-3 w-full rounded-lg lg:w-3/4">
                                                    </div>

                                                    {{-- ラベル追加ボタン　（スマホ　・　タブレット　用の表示） --}}
                                                    <div class="block mt-3 lg:hidden">
                                                        <label for="select_label"
                                                            class="px-6 py-1 text-xs font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50"
                                                            x-on:click="modal_label_select = true">ラベルを追加</label>
                                                        <input id="select_label" class="hidden"></input>
                                                    </div>

                                                    <div class="sm:mt-3">

                                                        {{-- タグ --}}
                                                        <div class="mt-12 lg:mt-32">
                                                            @livewire('label-attached')
                                                        </div>
                                                    </div>
                                                </div>


                                                {{-- 真ん中 --}}
                                                <div class="mt-6 lg:col-span-3 lg:mt-0">
                                                    <div>
                                                        @error('book_shortMemo')
                                                        <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                        @enderror
                                                        <label for="book_shortMemo" class="block">ひとことメモ<span
                                                                class="required">*</span></label>
                                                        <textarea id="book_shortMemo" x-ref="book_shortMemo" type="text"
                                                            wire:model.defer="book_shortMemo" class="w-full rounded-lg"
                                                            cols="60" rows="6"></textarea>
                                                    </div>

                                                    {{-- ラベル追加ボタン　（PC用　の表示） --}}
                                                    <div class="hidden mt-3 lg:block">
                                                        <label for="select_label"
                                                            class="px-6 py-1 text-xs font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50"
                                                            x-on:click="modal_label_select = true">ラベルを追加</label>
                                                        <input id="select_label" class="hidden"></input>
                                                    </div>
                                                </div>
                                                {{-- 右側 --}}
                                                <div class="grid grid-cols-5">
                                                    <div class="col-span-5">
                                                        <div class="m-auto max-w-xs">
                                                            <div class="hidden text-right lg:block">
                                                                <i class="text-xl fas fa-book-open"></i>
                                                            </div>

                                                            <div class="flex justify-center mt-5">
                                                                @if ($book_image_preview?->isPreviewable())
                                                                <img class="w-1/3 lg:w-3/4"
                                                                    src="{{ $book_image_preview->temporaryUrl() }}">
                                                                @elseif ($book_image_delete_flag)
                                                                <img class="w-1/3 lg:w-3/4" src="/images/svg/default-book.svg">
                                                                @else
                                                                <img class="w-1/3 lg:w-3/4" src="/images/svg/default-book.svg">
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <!-- TODO: バリデーションでエラーが起きた際の画像の復元は難しい問題なのであとでやる -->
                                                            @error('book_image_preview')
                                                            <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                            @enderror

                                                            <div class="flex gap-2 justify-center lg:flex-col">
                                                                <div class="text-center">
                                                                    <label for="book_image_preview"
                                                                        class="px-6 py-1 text-xs font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50">画像を選択</label>
                                                                    <input id="book_image_preview" class="hidden" type="file"
                                                                        wire:model.defer="book_image_preview"></input>
                                                                </div>
                                                                <div class="text-center">
                                                                    <button wire:click="deleteBookImage" type="button"
                                                                        class="px-6 py-1 text-xs font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50">画像を削除</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-10">
                                                <label for="book_additionalMemo" class="block">自由記入欄</label>
                                                <textarea id="book_additionalMemo" x-ref="book_additionalMemo"
                                                    type="text" class="w-full rounded-lg"
                                                    wire:model.defer="book_additionalMemo" rows="10"></textarea>
                                            </div>

                                            {{-- ボタン --}}
                                            <div class="mt-10 text-center">
                                                <button
                                                    class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 sm:px-24 focus:outline-none hover:bg-indigo-500"
                                                    wire:click="store('book')" type="button">メモ投稿</button>
                                            </div>
                                        </form>
                                    </div>
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
        class="flex fixed top-0 left-0 z-30 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
        <div x-cloak x-show=" modal_label_select"
            class="flex fixed top-0 left-0 z-30 justify-center items-center w-screen h-screen bg-opacity-40 border">
            <div x-on:click.away="modal_label_select = false"
                class="flex flex-col w-full max-h-[80%] max-w-[80%] md:max-w-[40%] px-3 py-2 bg-white rounded-xl overflow-auto">

                @livewire('label-adder')

            </div>
        </div>
    </div>

    {{-- ページに①通常アクセス or ②「戻るボタン」でアクセス　した際、 １：input（text）２：selectbox ３：textarea ４：checkbox ５：radiobutton をリセットするための処理
    --}}
    <script>
        function resetFormElements() {
                const selectElement = document.querySelector('select.max-w-xs');
                const inputElements = document.querySelectorAll('input:not([name="_token"])');
                const textareaElements = document.querySelectorAll('textarea');

                if (selectElement) {
                    selectElement.value = '';
                }
                inputElements.forEach(input => {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });
                textareaElements.forEach(textarea => {
                    textarea.value = '';
                });
            }

            window.addEventListener('load', resetFormElements);
    </script>

</div>
