<div>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
            グループ作成
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-10 py-24 bg-white shadow-xl sm:rounded-2xl">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 mx-auto">
                        <div class="-m-4 ">
                            <div class="p-4">
                                <div class="relative px-8 pt-8 pb-8 bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                                    <form class="grid sm:grid-cols-5" wire:submit.prevent='storeGroup'>

                                        {{-- 左側 --}}
                                        <div class="sm:col-span-3">
                                            {{-- photo --}}
                                            <div class="flex content-center">
                                                @if ($group_image?->isPreviewable())
                                                <div
                                                    class="object-cover mr-3 bg-center rounded-full h-14 sm:h-20 w-14 sm:w-20">
                                                    <img class="object-fill rounded-full sm:w-20 h-14 sm:h-20 w-14"
                                                        src="{{ $group_image->temporaryUrl() }}">
                                                </div>
                                                @else
                                                <div
                                                    class="object-cover mr-3 bg-blue-200 rounded-full w-14 sm:w-20 h-14 sm:h-20">
                                                </div>
                                                @endif

                                            </div>
                                            {{-- end_photo --}}

                                            {{-- 画像設定ボタン --}}
                                            @error('group_image')
                                            <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                            @enderror

                                            <div class="flex gap-4 mt-3 leading-none y-4">
                                                <div>
                                                    <label for="group_image"
                                                        class="px-6 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50">画像を選択</label>
                                                    <input id="group_image" class="hidden" type="file"
                                                        wire:model.defer="group_image"></input>
                                                </div>
                                                <div>
                                                    <label for="delete_photo"
                                                        class="px-6 py-1 text-sm font-bold text-gray-700 bg-white border border-gray-300 cursor-pointer rounded-2xl hover:bg-gray-50">画像を削除</label>
                                                    <input id="delete_photo" class="hidden" type="file"></input>
                                                </div>
                                            </div>

                                            {{-- グループ情報入力 --}}
                                            <div class="mt-5">
                                                @error('group_name')
                                                <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
                                                @enderror

                                                <label for="group_name" class="block text-sm">グループ名<span
                                                        class="required">*</span></label>
                                                <input wire:model.defer="group_name" id="group_name" type="text"
                                                    class="rounded-lg" size="30">
                                            </div>

                                            <div class="mt-5">
                                                @error('introduction')
                                                <li class="mt-3 text-sm text-red-600">{{ $message }}</li>
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
                                            class="flex items-center justify-center pt-10 sm:col-span-2 sm:justify-end sm:pt-0">
                                            <button
                                                class="px-16 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
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
</div>