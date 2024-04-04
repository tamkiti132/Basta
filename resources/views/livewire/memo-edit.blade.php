<div x-data="{ modal_label_select: false }" wire:init="$refresh">
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            メモ編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto text-sm max-w-7xl sm:px-6 lg:px-8">

            <div class="h-16">

                <div x-data="{ showMessage: false, message: '' }"
                    @flash-message.window="showMessage = true; message = $event.detail.message; setTimeout(() => showMessage = false, 4000);">
                    <div x-show="showMessage" x-cloak
                        class="w-1/2 p-2 mx-auto font-bold text-center text-white bg-blue-300 rounded-2xl">
                        <span x-text="message"></span>
                    </div>
                </div>
            </div>

            <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
                @if ($memo_data->type == 0)
                {{-- Webタイプの　場合 --}}
                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <form class="p-4" wire:submit.prevent="update">
                                <div
                                    class="relative px-4 pt-8 pb-16 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                                    <div class="grid lg:grid-cols-7">
                                        {{-- 左側 --}}
                                        <div class="lg:col-span-3">
                                            {{-- Webタイプのメモであることを示すデータ --}}
                                            <input type="hidden" value="web" wire:model.defer="memo_data.type">
                                            {{-- メモ情報 --}}
                                            <div>
                                                <div>
                                                    @error('memo_data.title')
                                                    <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                    @enderror
                                                </div>
                                                <label for="title" class="block">タイトル<span
                                                        class="required">*</span></label>
                                                <input id="title" type="text" wire:model.defer="memo_data.title"
                                                    class="w-full mb-3 rounded-lg lg:w-3/4">
                                            </div>
                                            <div class="mt-3">
                                                @error('memo_data.web_type_feature.url')
                                                <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                @enderror
                                                <label for="url" class="block">URL<span
                                                        class="required">*</span></label>
                                                <input id="url" type="text"
                                                    wire:model.defer="memo_data.web_type_feature.url"
                                                    class="w-full rounded-lg lg:w-3/4">
                                            </div>

                                            {{-- ラベル追加ボタン　（スマホ　・　タブレット　用の表示） --}}
                                            <div class="block mt-3 lg:hidden">
                                                <label for="select_label"
                                                    class="px-6 py-1 text-xs font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50"
                                                    x-on:click="modal_label_select = true">ラベルを選択</label>
                                                <input id="select_label" class="hidden"></input>
                                            </div>

                                            {{-- タグ --}}
                                            @livewire('label-attached-to-memo-list', ['memoId' => $memo_data->id ])
                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="mt-6 lg:col-span-3 lg:mt-0">
                                            <div>
                                                @error('memo_data.shortMemo')
                                                <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                @enderror
                                                <label for="shortMemo" class="block">ひとことメモ<span
                                                        class="required">*</span></label>
                                                <textarea id="shortMemo" type="text"
                                                    wire:model.defer="memo_data.shortMemo" class="w-full rounded-lg"
                                                    cols="60" rows="6"></textarea>
                                            </div>

                                            {{-- ラベル追加ボタン　（PC用　の表示） --}}
                                            <div class="hidden mt-3 lg:block">
                                                <label for="select_label"
                                                    class="px-6 py-1 text-xs font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50"
                                                    x-on:click="modal_label_select = true">ラベルを選択</label>
                                                <input id="select_label" class="hidden"></input>
                                            </div>
                                        </div>
                                        <div class="hidden text-right lg:block">
                                            <i class="text-xl fas fa-globe"></i>
                                        </div>
                                    </div>
                                    <div class="mt-10">
                                        @error('memo_data.additionalMemo')
                                        <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                        @enderror
                                        <label for="additionalMemo" class="block text-sm">自由記入欄</label>
                                        <textarea id="additionalMemo" type="text"
                                            wire:model.defer="memo_data.additionalMemo" class="w-full rounded-lg"
                                            rows="10"></textarea>
                                    </div>
                                    {{-- ボタン --}}
                                    <div class="mt-10 text-center">
                                        <button
                                            class="px-10 py-3 text-sm font-bold text-white bg-indigo-400 border-0 sm:px-24 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                            wire:click="update" type="button">メモ更新</button>
                                    </div>


                                </div>
                            </form>
                        </div>
                    </div>
                </section>
                @elseif ($memo_data->type == 1)
                {{-- 本タイプ　の場合 --}}
                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <form class="p-4" wire:submit.prevent="update">
                                <div
                                    class="relative px-4 pt-8 pb-16 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                                    <div class="grid lg:grid-cols-7">
                                        {{-- 左側 --}}
                                        {{-- Bookタイプのメモであることを示すデータ --}}
                                        <input type="hidden" name="memo_type" value="book">
                                        <div class="lg:col-span-3">
                                            {{-- メモ情報 --}}
                                            <div>
                                                @error('memo_data.title')
                                                <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                @enderror
                                                <label for="title" class="block">タイトル<span
                                                        class="required">*</span></label>
                                                <input id="title" type="text" wire:model.defer="memo_data.title"
                                                    class="w-full mb-3 rounded-lg lg:w-3/4">
                                            </div>

                                            <div class="block mt-3 lg:hidden">
                                                <label for="select_label"
                                                    class="px-6 py-1 text-xs font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50"
                                                    x-on:click="modal_label_select = true">ラベルを選択</label>
                                                <input id="select_label" class="hidden"></input>
                                            </div>

                                            {{-- タグ --}}
                                            <div class="mt-12 lg:mt-32">
                                                @livewire('label-attached-to-memo-list', ['memoId' => $memo_data->id])
                                            </div>
                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="mt-6 lg:col-span-3 lg:mt-0">
                                            <div>
                                                @error('memo_data.shortMemo')
                                                <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                @enderror
                                                <label for="shortMemo" class="block">ひとことメモ<span
                                                        class="required">*</span></label>
                                                <textarea id="shortMemo" type="text"
                                                    wire:model.defer="memo_data.shortMemo" class="w-full rounded-lg"
                                                    cols="60" rows="6"></textarea>
                                            </div>

                                            {{-- ラベル追加ボタン　（PC用　の表示） --}}
                                            <div class="hidden mt-3 lg:block">
                                                <label for="select_label"
                                                    class="px-6 py-1 text-xs font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50"
                                                    x-on:click="modal_label_select = true">ラベルを選択</label>
                                                <input id="select_label" class="hidden"></input>
                                            </div>
                                        </div>
                                        {{-- 右側 --}}
                                        <div class="grid grid-cols-5">
                                            <div class="col-span-5">
                                                <div class="max-w-xs m-auto">
                                                    <div class="hidden text-right lg:block">
                                                        <i class="text-xl fas fa-book-open"></i>
                                                    </div>

                                                    <div class="flex justify-center">
                                                        @if ($book_image_preview?->isPreviewable())
                                                        <img class="h-36 lg:h-auto"
                                                            src="{{ $book_image_preview->temporaryUrl() }}">
                                                        @elseif ($book_image_delete_flag)
                                                        <img class="h-36 lg:h-auto" src="/images/本の画像（青）.png">
                                                        @elseif($memo_data?->book_type_feature?->book_photo_path)
                                                        <img class="h-36 lg:h-auto"
                                                            src="{{ asset('storage/book-image/'. basename($memo_data->book_type_feature->book_photo_path)) }}" />
                                                        @else
                                                        <img class="h-36 lg:h-auto" src="/images/本の画像（青）.png">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <!-- TODO: バリデーションでエラーが起きた際の画像の復元は難しい問題なのであとでやる -->
                                                    @error('book_image_preview')
                                                    <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                    @enderror

                                                    <div class="flex justify-center gap-2 lg:flex-col">
                                                        <div class="text-center">
                                                            <label for="book_image_preview"
                                                                class="px-6 py-1 text-xs font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50">画像を選択</label>
                                                            <input id="book_image_preview" class="hidden" type="file"
                                                                wire:model.defer="book_image_preview"></input>
                                                        </div>
                                                        <div class="text-center">
                                                            <button wire:click="deleteBookImage" type="button"
                                                                class="px-6 py-1 text-xs font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50">画像を削除</button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-10">
                                        @error('memo_data.additionalMemo')
                                        <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                        @enderror
                                        <label for="additionalMemo" class="block text-sm">自由記入欄</label>
                                        <textarea id="additionalMemo" type="text"
                                            wire:model.defer="memo_data.additionalMemo"
                                            class="w-full text-sm rounded-lg sm:text-base" rows="10"></textarea>
                                    </div>
                                    {{-- ボタン --}}
                                    <div class="mt-10 text-center">
                                        <button
                                            class="px-10 py-3 font-bold text-white bg-indigo-400 border-0 sm:px-24 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                            wire:click="update" type="button">メモ更新</button>
                                    </div>


                                </div>
                        </div>
                    </div>
                </section>
            </div>

            @endif
        </div>
    </div>

    {{-- ラベル選択モーダル --}}
    <div x-cloak x-show=" modal_label_select"
        class="fixed top-0 left-0 z-30 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
        <div x-on:click.away="modal_label_select = false"
            class="flex flex-col w-full max-h-[80%] max-w-[80%] md:max-w-[40%] px-3 py-2 bg-white rounded-xl">

            @livewire('label-selector', ['memoId' => $memo_data->id ])

        </div>
    </div>
</div>