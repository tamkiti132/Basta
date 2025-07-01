<div>

    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800 whitespace-nowrap text-ellipsis">
            リクエストを送信
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="px-6 mx-auto max-w-7xl text-sm lg:text-base lg:px-8">
            <div class="overflow-hidden py-12 bg-white shadow-xl sm:rounded-2xl">
                <div class="mx-5 text-center lg:mx-auto lg:w-1/2">
                    {{-- 注意文 --}}
                    <div class="text-xs lg:text-sm">
                        <p>※ユーザー・グループの通報はここではしないでください。<br>
                            （運営者がその通報を見つけられなくなってしまいます）</p>
                    </div>

                    {{-- リクエスト入力欄 --}}
                    <div class="mt-20" id="request-input-container">
                        <div x-data="{ selectedType: '' }">
                            {{-- リクエスト種別 --}}
                            <label for="type" class="block mb-2 text-left">リクエスト種別をお選びください<span
                                    class="required">*</span></label>
                            <select name="type" id="type" class="w-full rounded-lg" x-model="selectedType">
                                <option hidden>-</option>
                                <option value="show_type_1">サービスの不具合の報告</option>
                                <option value="show_type_2">サービス機能の追加・改善リクエスト</option>
                                <option value="show_type_3">セキュリティ脆弱性の報告</option>
                                <option value="show_type_4">その他お問い合わせ</option>
                            </select>

                            <ul class="mt-8 w-auto">
                                {{-- サービスの不具合の報告 の場合 --}}
                                <li class="w-full" x-show="selectedType === 'show_type_1'">
                                    <form id="form1" wire:submit.prevent='sendRequest'>
                                        <div class="grid gap-8">
                                            {{-- 件名（お問い合わせのタイトル） --}}
                                            <div>
                                                @error('title_1')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="title_1" class="block mb-2 text-left">件名（お問い合わせのタイトル）<span
                                                        class="required">*</span></label>
                                                <input type="text" id="title_1" size="50" wire:model.defer="title_1"
                                                    class="py-2 w-full rounded-lg border border-gray-500">
                                            </div>
                                            {{-- 詳細 --}}
                                            <div>
                                                @error('detail_1')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="detail_1" class="block mb-2 text-left">詳細<span
                                                        class="required">*</span></label>
                                                <textarea id="detail_1" rows="5" wire:model.defer="detail_1" class="w-full rounded-lg border border-gray-500"></textarea>
                                            </div>
                                            {{-- ご利用環境 --}}
                                            <div>
                                                @error('environment_1')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="environment_1" class="block mb-2 text-left">ご利用環境<span
                                                        class="required">*</span></label>
                                                <select id="environment_1" class="w-full rounded-lg" wire:model.defer="environment_1">
                                                    <option hidden>-</option>
                                                    <option value="0">パソコンWindowsブラウザ</option>
                                                    <option value="1">パソコンMacブラウザ</option>
                                                    <option value="2">スマートフォンiPhoneブラウザ</option>
                                                    <option value="3">スマートフォンAndroidブラウザ</option>
                                                    <option value="4">タブレットAndroidブラウザ</option>
                                                    <option value="5">タブレットiPhoneブラウザ</option>
                                                    <option value="6">その他の環境</option>
                                                </select>
                                            </div>
                                            {{-- ご利用環境の補足 --}}
                                            <div>
                                                @error('additional_information')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="additional_information"
                                                    class="block mb-2 text-left">ご利用環境の補足</label>
                                                <textarea id="additional_information" cols="30" wire:model.defer="additional_information" rows="8"
                                                    class="py-2 w-full rounded-lg border border-gray-500"></textarea>
                                            </div>
                                            {{-- 参考URL --}}
                                            <div>
                                                @error('reference_url_1')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="reference_url_1" class="block mb-2 text-left">参考URL</label>
                                                <input type="url" id="reference_url_1" size="50"
                                                    wire:model.defer="reference_url_1"
                                                    class="py-2 w-full rounded-lg border border-gray-500">
                                            </div>
                                            {{-- 添付ファイル --}}
                                            <div>
                                                @error('uploaded_photo_1')
                                                    <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <p class="block mb-2 text-left">添付画像</p>
                                                <div class="flex gap-4 items-center mb-4">
                                                    <div class="text-left">
                                                        <label for="uploaded_photo_1" class="inline-block px-4 py-2 text-xs font-bold leading-none text-gray-700 bg-white rounded-2xl border border-gray-500 cursor-pointer hover:bg-gray-50">画像を選択</label>
                                                        <input type="file" id="uploaded_photo_1" wire:model.defer="uploaded_photo_1" class="hidden" accept="image/*">
                                                    </div>
                                                    <div class="text-left">
                                                        <button type="button" wire:click="delete_photo(1)" class="inline-block px-4 py-2 text-xs font-bold leading-none text-gray-700 bg-white rounded-2xl border border-gray-500 cursor-pointer hover:bg-gray-50">画像を削除</button>
                                                    </div>
                                                </div>

                                                @if ($uploaded_photo_1?->isPreviewable())
                                                    <img class="h-36 lg:h-auto"
                                                        src="{{ $uploaded_photo_1->temporaryUrl() }}">
                                                @endif
                                            </div>
                                        </div>

                                        {{-- ボタン --}}
                                        <div class="mt-10 text-center">
                                            <button
                                                class="px-24 py-3 text-lg font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
                                                wire:click="sendRequest('type_1')" type="button">送信</button>
                                        </div>
                                    </form>
                                </li>

                                {{-- サービス機能の追加・改善リクエスト --}}
                                <li class="w-full" x-show="selectedType === 'show_type_2'">
                                    <form id="form2" wire:submit.prevent='sendRequest'>
                                        <div class="grid gap-8">
                                            {{-- ご要望のタイプ --}}
                                            <div>
                                                @error('function_request_type')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="function_request_type" class="block mb-2 text-left">ご要望のタイプ<span
                                                        class="required">*</span></label>
                                                <select id="function_request_type" wire:model.defer="function_request_type"
                                                    class="w-full rounded-lg">
                                                    <option hidden>-</option>
                                                    <option value="0">新機能のリクエスト</option>
                                                    <option value="1">機能の改善案</option>
                                                    <option value="2">既存機能のバグ</option>
                                                    <option value="3">サービス全般</option>
                                                </select>
                                            </div>
                                            {{-- 件名（お問い合わせのタイトル） --}}
                                            <div>
                                                @error('title_2')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="title_2" class="block mb-2 text-left">件名（お問い合わせのタイトル）<span
                                                        class="required">*</span></label>
                                                <input type="text" id="title_2" size="50" wire:model.defer="title_2"
                                                    class="py-2 w-full rounded-lg border border-gray-500">
                                            </div>
                                            {{-- 詳細 --}}
                                            <div>
                                                @error('detail_2')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="detail_2" class="block mb-2 text-left">詳細<span
                                                        class="required">*</span></label>
                                                <textarea id="detail_2" cols="30" rows="8" wire:model.defer="detail_2"
                                                    class="py-2 w-full rounded-lg border border-gray-500"></textarea>
                                            </div>
                                            {{-- ご利用環境 --}}
                                            <div>
                                                @error('environment_2')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="environment_2" class="block mb-2 text-left">ご利用環境<span
                                                        class="required">*</span></label>
                                                <select id="environment_2" class="w-full rounded-lg"
                                                    wire:model.defer="environment_2">
                                                    <option hidden>-</option>
                                                    <option value="0">パソコンWindowsブラウザ</option>
                                                    <option value="1">パソコンMacブラウザ</option>
                                                    <option value="2">スマートフォンiPhoneブラウザ</option>
                                                    <option value="3">スマートフォンAndroidブラウザ</option>
                                                    <option value="4">タブレットAndroidブラウザ</option>
                                                    <option value="5">タブレットiPhoneブラウザ</option>
                                                    <option value="6">その他の環境</option>
                                                </select>
                                            </div>
                                            {{-- 参考URL --}}
                                            <div>
                                                @error('reference_url_2')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="reference_url_2" class="block mb-2 text-left">参考URL</label>
                                                <input type="url" id="reference_url_2" size="50"
                                                    wire:model.defer="reference_url_2"
                                                    class="py-2 w-full rounded-lg border border-gray-500">
                                            </div>
                                            {{-- 添付ファイル --}}
                                            <div>
                                                @error('uploaded_photo_2')
                                                    <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <p class="block mb-2 text-left">添付画像</p>
                                                <div class="flex gap-4 items-center mb-4">
                                                    <div class="text-left">
                                                        <label for="uploaded_photo_2" class="inline-block px-4 py-2 text-xs font-bold leading-none text-gray-700 bg-white rounded-2xl border border-gray-500 cursor-pointer hover:bg-gray-50">画像を選択</label>
                                                        <input type="file" id="uploaded_photo_2" wire:model.defer="uploaded_photo_2" class="hidden" accept="image/*">
                                                    </div>
                                                    <div class="text-left">
                                                        <button type="button" wire:click="delete_photo(2)" class="inline-block px-4 py-2 text-xs font-bold leading-none text-gray-700 bg-white rounded-2xl border border-gray-500 cursor-pointer hover:bg-gray-50">画像を削除</button>
                                                    </div>
                                                </div>
                                                @if ($uploaded_photo_2?->isPreviewable())
                                                    <img class="h-36 lg:h-auto"
                                                        src="{{ $uploaded_photo_2->temporaryUrl() }}">
                                                @endif
                                            </div>
                                        </div>

                                        {{-- ボタン --}}
                                        <div class="mt-10 text-center">
                                            <button
                                                class="px-24 py-3 text-lg font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
                                                wire:click="sendRequest('type_2')" type="button">送信</button>
                                        </div>
                                    </form>
                                </li>

                                {{-- セキュリティ脆弱性の報告 --}}
                                <li class="w-full" x-show="selectedType === 'show_type_3'">
                                    <form id="form3" wire:submit.prevent='sendRequest'>
                                        <div class="grid gap-8">
                                            {{-- 件名（お問い合わせのタイトル） --}}
                                            <div>
                                                @error('title_3')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="title_3" class="block mb-2 text-left">件名（お問い合わせのタイトル）<span
                                                        class="required">*</span></label>
                                                <input type="text" id="title_3" size="50" wire:model.defer="title_3"
                                                    class="py-2 w-full rounded-lg border border-gray-500">
                                            </div>
                                            {{-- 詳細 --}}
                                            <div>
                                                @error('detail_3')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="detail_3" class="block mb-2 text-left">詳細<span
                                                        class="required">*</span></label>
                                                <textarea id="detail_3" cols="30" rows="8" wire:model.defer="detail_3"
                                                    class="py-2 w-full rounded-lg border border-gray-500"></textarea>
                                            </div>
                                            {{-- 対象の脆弱性に関する技術的な説明 --}}
                                            <div>
                                                @error('explanation')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="explanation"
                                                    class="block mb-2 text-left">対象の脆弱性に関する技術的な説明</label>
                                                <textarea id="explanation" cols="30" rows="8" wire:model.defer="explanation"
                                                    class="py-2 w-full rounded-lg border border-gray-500"></textarea>
                                            </div>
                                            {{-- 対象の脆弱性の再現手順 --}}
                                            <div>
                                                @error('steps_to_reproduce')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="steps_to_reproduce" class="block mb-2 text-left">対象の脆弱性の再現手順<span
                                                        class="required">*</span></label>
                                                <textarea id="steps_to_reproduce" cols="30" rows="8" wire:model.defer="steps_to_reproduce"
                                                    class="py-2 w-full rounded-lg border border-gray-500"></textarea>
                                            </div>
                                            {{-- 対象の脆弱性の悪用方法 --}}
                                            <div>
                                                @error('abuse_method')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="abuse_method" class="block mb-2 text-left">対象の脆弱性の悪用方法</label>
                                                <textarea id="abuse_method" cols="30" rows="8" wire:model.defer="abuse_method"
                                                    class="py-2 w-full rounded-lg border border-gray-500"></textarea>
                                            </div>
                                            {{-- 対象の脆弱性の回避策 --}}
                                            <div>
                                                @error('workaround')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="workaround" class="block mb-2 text-left">対象の脆弱性の回避策</label>
                                                <textarea id="workaround" cols="30" rows="8" wire:model.defer="workaround"
                                                    class="py-2 w-full rounded-lg border border-gray-500"></textarea>
                                            </div>
                                            {{-- ご利用環境 --}}
                                            <div>
                                                @error('environment_3')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="environment_3" class="block mb-2 text-left">ご利用環境<span
                                                        class="required">*</span></label>
                                                <select id="environment_3" class="w-full rounded-lg"
                                                    wire:model.defer="environment_3">
                                                    <option hidden>-</option>
                                                    <option value="0">パソコンWindowsブラウザ</option>
                                                    <option value="1">パソコンMacブラウザ</option>
                                                    <option value="2">スマートフォンiPhoneブラウザ</option>
                                                    <option value="3">スマートフォンAndroidブラウザ</option>
                                                    <option value="4">タブレットAndroidブラウザ</option>
                                                    <option value="5">タブレットiPhoneブラウザ</option>
                                                    <option value="6">その他の環境</option>
                                                </select>
                                            </div>
                                            {{-- 参考URL --}}
                                            <div>
                                                @error('reference_url_3')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="reference_url_3" class="block mb-2 text-left">参考URL</label>
                                                <input type="url" id="reference_url_3" size="50"
                                                    wire:model.defer="reference_url_3"
                                                    class="py-2 w-full rounded-lg border border-gray-500">
                                            </div>
                                            {{-- 添付ファイル --}}
                                            <div>
                                                @error('uploaded_photo_3')
                                                    <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <p class="block mb-2 text-left">添付画像</p>
                                                <div class="flex gap-4 items-center mb-4">
                                                    <div class="text-left">
                                                        <label for="uploaded_photo_3" class="inline-block px-4 py-2 text-xs font-bold leading-none text-gray-700 bg-white rounded-2xl border border-gray-500 cursor-pointer hover:bg-gray-50">画像を選択</label>
                                                        <input type="file" id="uploaded_photo_3" wire:model.defer="uploaded_photo_3" class="hidden" accept="image/*">
                                                    </div>
                                                    <div class="text-left">
                                                        <button type="button" wire:click="delete_photo(3)" class="inline-block px-4 py-2 text-xs font-bold leading-none text-gray-700 bg-white rounded-2xl border border-gray-500 cursor-pointer hover:bg-gray-50">画像を削除</button>
                                                    </div>
                                                </div>
                                                @if ($uploaded_photo_3?->isPreviewable())
                                                    <img class="h-36 lg:h-auto"
                                                        src="{{ $uploaded_photo_3->temporaryUrl() }}">
                                                @endif
                                            </div>
                                        </div>

                                        {{-- ボタン --}}
                                        <div class="mt-10 text-center">
                                            <button
                                                class="px-24 py-3 text-lg font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
                                                wire:click="sendRequest('type_3')" type="button">送信</button>
                                        </div>
                                    </form>
                                </li>

                                {{-- その他お問い合わせ --}}
                                <li class="w-full" x-show="selectedType === 'show_type_4'">
                                    <form id="form4" wire:submit.prevent='sendRequest'>
                                        <div class="grid gap-8">
                                            {{-- 件名（お問い合わせのタイトル） --}}
                                            <div>
                                                @error('title_4')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="title_4" class="block mb-2 text-left">件名（お問い合わせのタイトル）<span
                                                        class="required">*</span></label>
                                                <input type="text" id="title_4" size="50" wire:model.defer="title_4"
                                                    class="py-2 w-full rounded-lg border border-gray-500">
                                            </div>
                                            {{-- 詳細 --}}
                                            <div>
                                                @error('detail_4')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="detail_4" class="block mb-2 text-left">詳細<span
                                                        class="required">*</span></label>
                                                <textarea id="detail_4" cols="30" rows="8" wire:model.defer="detail_4"
                                                    class="py-2 w-full rounded-lg border border-gray-500"></textarea>
                                            </div>
                                            {{-- リクエスト種別 --}}
                                            <div>
                                                @error('environment_4')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="environment4" class="block mb-2 text-left">ご利用環境<span
                                                        class="required">*</span></label>
                                                <select id="environment_4" class="w-full rounded-lg"
                                                    wire:model.defer="environment_4">
                                                    <option hidden>-</option>
                                                    <option value="0">パソコンWindowsブラウザ</option>
                                                    <option value="1">パソコンMacブラウザ</option>
                                                    <option value="2">スマートフォンiPhoneブラウザ</option>
                                                    <option value="3">スマートフォンAndroidブラウザ</option>
                                                    <option value="4">タブレットAndroidブラウザ</option>
                                                    <option value="5">タブレットiPhoneブラウザ</option>
                                                    <option value="6">その他の環境</option>
                                                </select>
                                            </div>
                                            {{-- 参考URL --}}
                                            <div>
                                                @error('reference_url_4')
                                                <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <label for="reference_url_4" class="block mb-2 text-left">参考URL</label>
                                                <input type="url" id="reference_url_4" size="50"
                                                    wire:model.defer="reference_url_4"
                                                    class="py-2 w-full rounded-lg border border-gray-500">
                                            </div>
                                            {{-- 添付ファイル --}}
                                            <div>
                                                @error('uploaded_photo_4')
                                                    <p class="text-xs text-left text-red-600">{{ $message }}</p>
                                                @enderror
                                                <p class="block mb-2 text-left">添付画像</p>
                                                <div class="flex gap-4 items-center mb-4">
                                                    <div class="text-left">
                                                        <label for="uploaded_photo_4" class="inline-block px-4 py-2 text-xs font-bold leading-none text-gray-700 bg-white rounded-2xl border border-gray-500 cursor-pointer hover:bg-gray-50">画像を選択</label>
                                                        <input type="file" id="uploaded_photo_4" wire:model.defer="uploaded_photo_4" class="hidden" accept="image/*">
                                                    </div>
                                                    <div class="text-left">
                                                        <button type="button" wire:click="delete_photo(4)" class="inline-block px-4 py-2 text-xs font-bold leading-none text-gray-700 bg-white rounded-2xl border border-gray-500 cursor-pointer hover:bg-gray-50">画像を削除</button>
                                                    </div>
                                                </div>
                                                @if ($uploaded_photo_4?->isPreviewable())
                                                    <img class="h-36 lg:h-auto"
                                                        src="{{ $uploaded_photo_4->temporaryUrl() }}">
                                                @endif
                                            </div>
                                        </div>


                                        {{-- ボタン --}}
                                        <div class="mt-10 text-center">
                                            <button
                                                class="px-24 py-3 text-sm font-bold text-white bg-indigo-400 rounded-2xl border-0 focus:outline-none hover:bg-indigo-500"
                                                wire:click="sendRequest('type_4')" type="button">送信</button>
                                        </div>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div>

    <script>

        //  ページに①通常アクセス or ②「戻るボタン」でアクセス　した際、 １：input 　２：テキストエリア と ３：セレクトボックス をリセットするための処理
        function resetFormElements() {
            const container = document.querySelector('#request-input-container');
            const selectElements = container.querySelectorAll('select');
            const inputElements = container.querySelectorAll('input:not([name="_token"])');
            const textareaElements = container.querySelectorAll("textarea");

            selectElements.forEach((select) => {
                select.value = "";
            });
            inputElements.forEach((input) => {
                input.value = "";
            });
            textareaElements.forEach((textarea) => {
                textarea.value = "";
            });
        }

        window.addEventListener("load", resetFormElements);
    </script>


  {{-- このCSSの記述を書いた理由は、
    x-showで表示されるtextareaの高さが狭くなってしまう不具合の応急処置をするため --}}
    <style>
        #detail_1, #detail_2, #detail_3, #detail_4 {
            min-height: 160px; /* 開始時の最小高さ */
        }

        #additional_information, #explanation, #steps_to_reproduce, #abuse_method, #workaround {
            min-height: 216px; /* 開始時の最小高さ */
        }
    </style>


    <script>
        document.addEventListener('livewire:load', function () {
            window.addEventListener('reloadPage', function () {
                location.reload();
            });
        });
    </script>
</div>