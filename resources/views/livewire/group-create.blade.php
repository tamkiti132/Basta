<div>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800">
            グループ作成
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl text-xs sm:px-6 lg:px-8">
            <div class="grid gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">
                <section class="text-gray-600 body-font">
                    <div class="px-5 mx-auto">
                        <div class="-m-4">
                            <div class="p-4">
                                <div class="relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 rounded-2xl shadow-md">
                                    <form class="grid sm:grid-cols-5" wire:submit.prevent='storeGroup'>

                                        {{-- 左側 --}}
                                        <div class="sm:col-span-3">
                                            {{-- photo --}}
                                            <div class="flex content-center">
                                                @if ($group_image?->isPreviewable())
                                                <div class="object-cover mr-3 w-14 h-14 bg-center rounded-full">
                                                    <img class="object-fill w-14 h-14 rounded-full"
                                                        src="{{ $group_image->temporaryUrl() }}">
                                                </div>
                                                @else
                                                <div class="object-cover mr-3 w-14 h-14 bg-blue-200 rounded-full">
                                                </div>
                                                @endif

                                            </div>
                                            {{-- end_photo --}}

                                            {{-- 画像設定ボタン --}}
                                            @error('group_image')
                                            <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                            @enderror

                                            <div class="flex flex-wrap gap-4 items-center mt-3 leading-none y-4">
                                                <div>
                                                    <label for="group_image"
                                                        class="px-6 py-1 font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50">画像を選択</label>
                                                    <input id="group_image" class="hidden" type="file"
                                                        wire:model.defer="group_image"></input>
                                                </div>
                                                <div>
                                                    <button wire:click="deleteGroupImagePreview" type="button"
                                                        class="px-6 py-1 font-bold text-gray-700 bg-white rounded-2xl border border-gray-300 cursor-pointer hover:bg-gray-50">画像を削除</button>
                                                </div>
                                            </div>

                                            {{-- グループ情報入力 --}}
                                            <div class="mt-5">
                                                @error('group_name')
                                                <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                @enderror

                                                <label for="group_name" class="block text-sm">グループ名<span
                                                        class="required">*</span></label>
                                                <input wire:model.defer="group_name" id="group_name" type="text"
                                                    class="w-full rounded-lg">
                                            </div>

                                            <div class="mt-5">
                                                @error('introduction')
                                                <li class="mt-3 text-xs text-red-600">{{ $message }}</li>
                                                @enderror

                                                <label for="introduction" class="block text-sm">グループ紹介文<span
                                                        class="required">*</span></label>
                                                <textarea wire:model.defer="introduction" id="introduction" type="text"
                                                    class="w-full rounded-lg" rows="6"></textarea>
                                            </div>

                                        </div>
                                        {{-- 右側 --}}
                                        {{-- ボタン --}}
                                        <div
                                            class="flex justify-center items-center pt-10 sm:col-span-2 sm:justify-end sm:pt-0">
                                            <button
                                                class="px-16 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
                                                type="submit">グループ作成</button>
                                        </div>
                                    </form>
                                </div>


                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    {{-- ページに①通常アクセス or ②「戻るボタン」でアクセス　した際、 １：input 　２：テキストエリア と ３：セレクトボックス をリセットするための処理 --}}
    <script>
        function resetFormElements() {
        const selectElement = document.querySelector('select.max-w-xs');
        const inputElements = document.querySelectorAll('input:not([name="_token"])');
        const textareaElements = document.querySelectorAll('textarea');

        if (selectElement) {
        selectElement.value = '';
        }
        inputElements.forEach(input => {
        input.value = '';
        });
        textareaElements.forEach(textarea => {
        textarea.value = '';
        });
        }

        window.addEventListener('load', resetFormElements);
    </script>

</div>