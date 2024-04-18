<div>

    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-800 whitespace-nowrap text-ellipsis">
            リクエストを送信
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="px-6 mx-auto text-sm lg:text-base max-w-7xl lg:px-8">
            <div class="py-12 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
                <div class="mx-5 text-center lg:mx-auto lg:w-1/2">
                    {{-- 注意文 --}}
                    <div class="text-xs lg:text-sm">
                        <p>※ユーザー・グループの通報はここではしないでください。<br>
                            （運営者がその通報を見つけられなくなってしまいます）</p>
                    </div>

                    {{-- リクエスト入力欄 --}}
                    <div class="mt-20">
                        {{-- リクエスト種別 --}}
                        <label for="type" class="block mb-2 text-left">リクエスト種別をお選びください<span
                                class="required">*</span></label>
                        <select name="type" id="type" class="w-full rounded-lg">
                            <option hidden>-</option>
                            <option value="request_type_0">サービスの不具合の報告</option>
                            <option value="request_type_1">サービス機能の追加・改善リクエスト</option>
                            <option value="request_type_2">セキュリティ脆弱性の報告</option>
                            <option value="request_type_3">その他お問い合わせ</option>
                        </select>

                        <ul class="w-auto">
                            {{-- サービスの不具合の報告 の場合 --}}
                            <li class="hidden w-full" id="request_type_0">
                                <form id="form1" action="">
                                    {{-- メールアドレス --}}
                                    <label for="email" class="block mt-8 mb-2 text-left">メールアドレス<span
                                            class="required">*</span></label>
                                    <input type="email" id="email" size="50"
                                        class="w-full border border-gray-500 rounded-lg">

                                    {{-- 件名（お問い合わせのタイトル） --}}
                                    <label for="title" class="block mt-8 mb-2 text-left">件名（お問い合わせのタイトル）<span
                                            class="required">*</span></label>
                                    <input type="text" name="title" id="title" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 詳細 --}}
                                    <label for="detail" class="block mt-8 mb-2 text-left">詳細<span
                                            class="required">*</span></label>
                                    <textarea name="detail" id="detail" rows="5"
                                        class="w-full border border-gray-500 rounded-lg"></textarea>

                                    {{-- ご利用環境 --}}
                                    <label for="environment" class="block mt-8 mb-2 text-left">ご利用環境<span
                                            class="required">*</span></label>
                                    <select name="environment" id="environment" class="w-full rounded-lg">
                                        <option hidden>-</option>
                                        <option value="0">パソコンWindowsブラウザ</option>
                                        <option value="1">パソコンMacブラウザ</option>
                                        <option value="2">スマートフォンiPhoneブラウザ</option>
                                        <option value="3">スマートフォンAndroidブラウザ</option>
                                        <option value="4">タブレットAndroidブラウザ</option>
                                        <option value="5">タブレットiPhoneブラウザ</option>
                                        <option value="6">その他の環境</option>
                                    </select>

                                    {{-- ご利用環境の補足 --}}
                                    <label for="additional_information"
                                        class="block mt-8 mb-2 text-left">ご利用環境の補足</label>
                                    <textarea name="additional_information" id="additional_information" cols="30"
                                        rows="8" class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                                    {{-- 参考URL --}}
                                    <label for="reference_url" class="block mt-8 mb-2 text-left">参考URL</label>
                                    <input type="url" name="reference_url" id="reference_url" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 添付ファイル --}}
                                    <p class="block mt-8 mb-2 text-left">添付ファイル</p>

                                    <x-attached-file-of-request-page uniqueId="uniqueId1" />

                                    {{-- ボタン --}}
                                    <div class="mt-10 text-center ">
                                        <button
                                            class="px-24 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                            type="">送信</button>
                                    </div>
                                </form>
                            </li>

                            {{-- サービス機能の追加・改善リクエスト --}}
                            <li class="hidden" id="request_type_1">
                                <form id="form2" action="">
                                    {{-- メールアドレス --}}
                                    <label for="email2" class="block mt-8 mb-2 text-left">メールアドレス<span
                                            class="required">*</span></label>
                                    <input type="email" id="email2" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- ご要望のタイプ --}}
                                    <label for="function_request_type" class="block mt-8 mb-2 text-left">ご要望のタイプ<span
                                            class="required">*</span></label>
                                    <select name="function_request_type" id="function_request_type"
                                        class="w-full rounded-lg">
                                        <option hidden>-</option>
                                        <option value="0">新機能のリクエスト</option>
                                        <option value="1">機能の改善案</option>
                                        <option value="2">既存機能のバグ</option>
                                        <option value="3">サービス全般</option>
                                    </select>

                                    {{-- 件名（お問い合わせのタイトル） --}}
                                    <label for="title2" class="block mt-8 mb-2 text-left">件名（お問い合わせのタイトル）<span
                                            class="required">*</span></label>
                                    <input type="text" id="title2" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 詳細 --}}
                                    <label for="detail2" class="block mt-8 mb-2 text-left">詳細<span
                                            class="required">*</span></label>
                                    <textarea name="detail2" id="detail2" cols="30" rows="8"
                                        class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                                    {{-- ご利用環境 --}}
                                    <label for="environment2" class="block mt-8 mb-2 text-left">ご利用環境<span
                                            class="required">*</span></label>
                                    <select name="environment2" id="environment2" class="w-full rounded-lg">
                                        <option hidden>-</option>
                                        <option value="0">パソコンWindowsブラウザ</option>
                                        <option value="1">パソコンMacブラウザ</option>
                                        <option value="2">スマートフォンiPhoneブラウザ</option>
                                        <option value="3">スマートフォンAndroidブラウザ</option>
                                        <option value="4">タブレットAndroidブラウザ</option>
                                        <option value="5">タブレットiPhoneブラウザ</option>
                                        <option value="6">その他の環境</option>
                                    </select>

                                    {{-- 参考URL --}}
                                    <label for="reference_url2" class="block mt-8 mb-2 text-left">参考URL</label>
                                    <input type="url" id="reference_url2" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 添付ファイル --}}
                                    <p class="block mt-8 mb-2 text-left">添付ファイル</p>

                                    <x-attached-file-of-request-page uniqueId="uniqueId2" />

                                    {{-- ボタン --}}
                                    <div class="mt-10 text-center">
                                        <button
                                            class="px-24 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                            type="">送信</button>
                                    </div>
                                </form>
                            </li>

                            {{-- セキュリティ脆弱性の報告 --}}
                            <li class="hidden" id="request_type_2">
                                <form id="form3" action="">
                                    {{-- メールアドレス --}}
                                    <label for="email3" class="block mt-8 mb-2 text-left">メールアドレス<span
                                            class="required">*</span></label>
                                    <input type="email" id="email3" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 件名（お問い合わせのタイトル） --}}
                                    <label for="title3" class="block mt-8 mb-2 text-left">件名（お問い合わせのタイトル）<span
                                            class="required">*</span></label>
                                    <input type="text" id="title3" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 詳細 --}}
                                    <label for="detail3" class="block mt-8 mb-2 text-left">詳細<span
                                            class="required">*</span></label>
                                    <textarea name="detail3" id="detail3" cols="30" rows="8"
                                        class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                                    {{-- 対象の脆弱性に関する技術的な説明 --}}
                                    <label for="explanation" class="block mt-8 mb-2 text-left">対象の脆弱性に関する技術的な説明</label>
                                    <textarea name="explanation" id="explanation" cols="30" rows="8"
                                        class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                                    {{-- 対象の脆弱性の再現手順 --}}
                                    <label for="steps_to_reproduce" class="block mt-8 mb-2 text-left">対象の脆弱性の再現手順<span
                                            class="required">*</span></label>
                                    <textarea name="steps_to_reproduce" id="steps_to_reproduce" cols="30" rows="8"
                                        class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                                    {{-- 対象の脆弱性の悪用方法 --}}
                                    <label for="abuse_method" class="block mt-8 mb-2 text-left">対象の脆弱性の悪用方法</label>
                                    <textarea name="abuse_method" id="abuse_method" cols="30" rows="8"
                                        class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                                    {{-- 対象の脆弱性の回避策 --}}
                                    <label for="workaround" class="block mt-8 mb-2 text-left">対象の脆弱性の回避策</label>
                                    <textarea name="workaround" id="workaround" cols="30" rows="8"
                                        class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                                    {{-- ご利用環境 --}}
                                    <label for="environment3" class="block mt-8 mb-2 text-left">ご利用環境<span
                                            class="required">*</span></label>
                                    <select name="environment3" id="environment3" class="w-full rounded-lg">
                                        <option hidden>-</option>
                                        <option value="0">パソコンWindowsブラウザ</option>
                                        <option value="1">パソコンMacブラウザ</option>
                                        <option value="2">スマートフォンiPhoneブラウザ</option>
                                        <option value="3">スマートフォンAndroidブラウザ</option>
                                        <option value="4">タブレットAndroidブラウザ</option>
                                        <option value="5">タブレットiPhoneブラウザ</option>
                                        <option value="6">その他の環境</option>
                                    </select>

                                    {{-- 参考URL --}}
                                    <label for="reference_url3" class="block mt-8 mb-2 text-left">参考URL</label>
                                    <input type="url" name="reference_url3" id="reference_url3" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 添付ファイル --}}
                                    <p class="block mt-8 mb-2 text-left">添付ファイル</p>

                                    <x-attached-file-of-request-page uniqueId="uniqueId3" />

                                    {{-- ボタン --}}
                                    <div class="mt-10 text-center">
                                        <button
                                            class="px-24 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                            type="">送信</button>
                                    </div>
                                </form>
                            </li>

                            {{-- その他お問い合わせ --}}
                            <li class="hidden" id="request_type_3">
                                <form id="form4" action="">
                                    {{-- メールアドレス --}}
                                    <label for="email4" class="block mt-8 mb-2 text-left">メールアドレス<span
                                            class="required">*</span></label>
                                    <input type="email" name="email4" id="email4" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 件名（お問い合わせのタイトル） --}}
                                    <label for="title4" class="block mt-8 mb-2 text-left">件名（お問い合わせのタイトル）<span
                                            class="required">*</span></label>
                                    <input type="text" name="title4" id="title4" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 詳細 --}}
                                    <label for="detail4" class="block mt-8 mb-2 text-left">詳細<span
                                            class="required">*</span></label>
                                    <textarea name="detail4" id="detail4" cols="30" rows="8"
                                        class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                                    {{-- リクエスト種別 --}}
                                    <label for="environment4" class="block mt-8 mb-2 text-left">ご利用環境<span
                                            class="required">*</span></label>
                                    <select name="environment4" id="environment4" class="w-full rounded-lg">
                                        <option hidden>-</option>
                                        <option value="0">パソコンWindowsブラウザ</option>
                                        <option value="1">パソコンMacブラウザ</option>
                                        <option value="2">スマートフォンiPhoneブラウザ</option>
                                        <option value="3">スマートフォンAndroidブラウザ</option>
                                        <option value="4">タブレットAndroidブラウザ</option>
                                        <option value="5">タブレットiPhoneブラウザ</option>
                                        <option value="6">その他の環境</option>
                                    </select>

                                    {{-- 参考URL --}}
                                    <label for="reference_url4" class="block mt-8 mb-2 text-left">参考URL</label>
                                    <input type="url" id="reference_url4" size="50"
                                        class="w-full py-2 border border-gray-500 rounded-lg">

                                    {{-- 添付ファイル --}}
                                    <p for="reference_url" class="block mt-8 mb-2 text-left">添付ファイル</p>

                                    <x-attached-file-of-request-page uniqueId="uniqueId4" />

                                    {{-- ボタン --}}
                                    <div class="mt-10 text-center">
                                        <button
                                            class="px-24 py-3 text-sm font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                                            type="">送信</button>
                                    </div>
                                </form>
                            </li>
                        </ul>

                    </div>

                </div>


            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.file-add-button').forEach(function(button) {
            button.addEventListener('click', function() {
                var target = this.getAttribute('data-target');
                var fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/*';
                fileInput.name = 'uploaded_file';
                fileInput.style.display = 'none';

                // ファイル選択後のイベント
                fileInput.addEventListener('change', function(event) {
                    handleFileSelect(event, target, fileInput);
                    var formElement = button.closest('form');
                    formElement.appendChild(fileInput);
                });

                document.body.appendChild(fileInput);
                fileInput.click();
            });
        });
        });

        function handleFileSelect(event, targetId, fileInput) {
            var file = event.target.files[0];
            var fileListContainer = document.getElementById(targetId);

            var fileDisplayElement = document.createElement('div');
            fileDisplayElement.className = 'file-display-element';

            var fileNameElement = document.createElement('span');
            fileNameElement.className = 'file-name';
            fileNameElement.textContent = file.name;
            fileDisplayElement.appendChild(fileNameElement);

            var deleteButton = document.createElement('button');
            deleteButton.className = 'delete-button';
            deleteButton.textContent = '×';
            deleteButton.addEventListener('click', function() {
                fileListContainer.removeChild(fileDisplayElement);
                if (fileInput.parentElement) {
                    fileInput.parentElement.removeChild(fileInput);
                }
            });
            fileDisplayElement.appendChild(deleteButton);

            fileListContainer.appendChild(fileDisplayElement);
        }

    </script>
</div>