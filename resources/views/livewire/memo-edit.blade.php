<div x-data="{ modal_label_select: false }">
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
            メモ編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

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
                    <div class="container px-5 mx-auto">
                        <div class="flex flex-wrap justify-center -m-4">
                            <form class="p-4" wire:submit.prevent="update">

                                <div
                                    class="relative px-4 pt-8 pb-16 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md sm:px-8 rounded-2xl">
                                    <div class="grid xl:grid-cols-7">
                                        {{-- 左側 --}}
                                        <div class="xl:col-span-3">
                                            {{-- Webタイプのメモであることを示すデータ --}}
                                            <input type="hidden" value="web" wire:model.defer="memo_data.type">
                                            {{-- メモ情報 --}}
                                            <div>
                                                <div>
                                                    @error('memo_data.title')
                                                    <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                                    @enderror
                                                </div>
                                                <label for="title" class="block text-sm">タイトル<span
                                                        class="required">*</span></label>
                                                <input id="title" type="text" wire:model.defer="memo_data.title"
                                                    class="w-full mb-3 text-sm rounded-lg sm:w-3/4 sm:text-base">
                                            </div>
                                            <div class="mt-3">
                                                @error('memo_data.web_type_feature.url')
                                                <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                                @enderror
                                                <label for="url" class="block text-sm">URL<span
                                                        class="required">*</span></label>
                                                <input id="url" type="text" {{-- name="url" --}}
                                                    wire:model.defer="memo_data.web_type_feature.url"
                                                    class="w-full text-sm rounded-lg sm:w-3/4 sm:text-base">
                                            </div>
                                            {{-- タグ --}}
                                            @livewire('label-attached-to-memo-list', ['memoId' => $memo_data->id ])
                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="mt-6 xl:col-span-3 xl:mt-0">
                                            <div>
                                                @error('memo_data.shortMemo')
                                                <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                                @enderror
                                                <label for="shortMemo" class="block text-sm">ひとことメモ<span
                                                        class="required">*</span></label>
                                                <textarea id="shortMemo" type="text"
                                                    wire:model.defer="memo_data.shortMemo"
                                                    class="w-full text-sm rounded-lg sm:text-base" cols="60"
                                                    rows="6"></textarea>
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
                                        {{--
                                        <x-validation-error name="additionalMemo" /> --}}
                                        @error('memo_data.additionalMemo')
                                        <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                        @enderror
                                        <label for="additionalMemo" class="block text-sm">自由記入欄</label>
                                        <textarea id="additionalMemo" type="text"
                                            wire:model.defer="memo_data.additionalMemo"
                                            class="w-full text-sm rounded-lg sm:text-base" rows="10"></textarea>
                                    </div>
                                    {{-- ボタン --}}
                                    <div class="mt-10 text-center">
                                        <button
                                            class="px-10 py-3 text-base font-bold text-white bg-indigo-400 border-0 sm:px-24 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500"
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
                    <div class="container px-5 mx-auto">
                        <div class="flex flex-wrap justify-center -m-4">
                            <form class="p-4" wire:submit.prevent="update">
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
                                                @error('memo_data.title')
                                                <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                                @enderror
                                                <label for="title" class="block text-sm">タイトル<span
                                                        class="required">*</span></label>
                                                <input id="title" type="text" wire:model.defer="memo_data.title"
                                                    class="w-full mb-3 text-sm rounded-lg sm:w-3/4 sm:text-base">
                                            </div>
                                            {{-- タグ --}}
                                            <div class="mt-12 sm:mt-32">
                                                @livewire('label-attached-to-memo-list', ['memoId' => $memo_data->id])
                                            </div>
                                        </div>
                                        {{-- 真ん中 --}}
                                        <div class="mt-6 xl:col-span-3 xl:mt-0">
                                            <div>
                                                @error('memo_data.shortMemo')
                                                <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                                @enderror
                                                <label for="shortMemo" class="block text-sm">ひとことメモ<span
                                                        class="required">*</span></label>
                                                <textarea id="shortMemo" type="text"
                                                    wire:model.defer="memo_data.shortMemo"
                                                    class="w-full text-sm rounded-lg sm:text-base" cols="60"
                                                    rows="6"></textarea>
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
                                            <div class="col-span-5">
                                                <div class="max-w-xs m-auto">
                                                    <div class="hidden text-right xl:block">
                                                        <i class="text-3xl fas fa-book-open"></i>
                                                    </div>

                                                    <div>
                                                        @if ($book_image?->isPreviewable())
                                                        <img src="{{ $book_image->temporaryUrl() }}">
                                                        @elseif($memo_data?->book_type_feature->book_photo_path)
                                                        <img
                                                            src="{{ asset('storage/book-image/'. basename($memo_data->book_type_feature->book_photo_path)) }}" />
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <!-- TODO: バリデーションでエラーが起きた際の画像の復元は難しい問題なのであとでやる -->
                                                    @error('book_image')
                                                    <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                                    @enderror
                                                    <label for="book_image"
                                                        class="px-2 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50">画像を選択</label>
                                                    <input id="book_image" class="hidden" type="file"
                                                        wire:model.defer="book_image"></input>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-10">
                                        @error('memo_data.additionalMemo')
                                        <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                        @enderror
                                        <label for="additionalMemo" class="block text-sm">自由記入欄</label>
                                        <textarea id="additionalMemo" type="text"
                                            wire:model.defer="memo_data.additionalMemo"
                                            class="w-full text-sm rounded-lg sm:text-base" rows="10"></textarea>
                                    </div>
                                    {{-- ボタン --}}
                                    <div class="mt-10 text-center">
                                        <button
                                            class="px-10 py-3 text-base font-bold text-white bg-indigo-400 border-0 sm:px-24 sm:text-lg rounded-2xl focus:outline-none hover:bg-indigo-500"
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
            class="flex flex-col w-full h-auto max-w-xs px-3 py-2 bg-white rounded-xl">

            @livewire('label-selector', ['memoId' => $memo_data->id ])

        </div>
    </div>
</div>