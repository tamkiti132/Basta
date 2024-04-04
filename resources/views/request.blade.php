<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold leading-tight text-gray-800">
      リクエストを送信
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="px-6 mx-auto text-sm lg:text-base max-w-7xl lg:px-8">
      <div class="grid gap-10 py-12 overflow-hidden bg-white shadow-xl sm:rounded-2xl">
        <div class="mx-5 text-center lg:mx-auto lg:w-1/2">
          {{-- 注意文 --}}
          <div class="text-xs lg:text-sm">
            <p>※ユーザー・グループの通報はここではしないでください。<br>
              （運営者がその通報を見つけられなくなってしまいます）</p>
          </div>

          {{-- リクエスト入力欄 --}}
          <div class="mt-20">
            {{-- リクエスト種別 --}}
            <label for="request_type" class="block mb-2 text-left">リクエスト種別をお選びください<span
                class="required">*</span></label>
            <select name="request_type" id="type" class="w-full rounded-lg">
              <option hidden>-</option>
              <option value="request_type_0">サービスの不具合の報告</option>
              <option value="request_type_1">サービス機能の追加・改善リクエスト</option>
              <option value="request_type_2">セキュリティ脆弱性の報告</option>
              <option value="request_type_3">その他お問い合わせ</option>
            </select>

            <ul>
              {{-- サービスの不具合の報告 の場合 --}}
              <li class="hidden" id="request_type_0">
                {{-- メールアドレス --}}
                <label for="email" class="block mt-8 mb-2 text-left">メールアドレス<span class="required">*</span></label>
                <input type="email" id="email" size="50" class="w-full border border-gray-500 rounded-lg">

                {{-- 件名（お問い合わせのタイトル） --}}
                <label for="title" class="block mt-8 mb-2 text-left">件名（お問い合わせのタイトル）<span
                    class="required">*</span></label>
                <input type="text" id="title" size="50" class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 詳細 --}}
                <label for="detail" class="block mt-8 mb-2 text-left">詳細<span class="required">*</span></label>
                <textarea name="detail" id="detail" rows="5"
                  class="w-full border border-gray-500 rounded-lg"></textarea>

                {{-- リクエスト種別 --}}
                <label for="environment" class="block mt-8 mb-2 text-left">ご利用環境<span class="required">*</span></label>
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
                <label for="additional_information" class="block mt-8 mb-2 text-left">ご利用環境の補足</label>
                <textarea name="additional_information" id="additional_information" cols="30" rows="8"
                  class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                {{-- 参考URL --}}
                <label for="reference_url" class="block mt-8 mb-2 text-left">参考URL</label>
                <input type="reference_url" id="reference_url" size="50"
                  class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 添付ファイル --}}
                <p for="reference_url" class="block mt-8 mb-2 text-left">添付ファイル</p>
                <label for="attached_file_path"
                  class="block w-full py-2 text-center border border-gray-500 rounded-lg cursor-pointer">ファイルを追加</label>
                <input class="hidden" id="attached_file_path" type="file">

                {{-- ボタン --}}
                <div class="mt-10 text-center">
                  <button
                    class="px-24 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                    type="">送信</button>
                </div>
              </li>

              {{-- サービス機能の追加・改善リクエスト --}}
              <li class="hidden" id="request_type_1">
                {{-- メールアドレス --}}
                <label for="mail" class="block mt-8 mb-2 text-left">メールアドレス<span class="required">*</span></label>
                <input type="mail" id="mail" size="50" class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- ご要望のタイプ --}}
                <label for="function_request_type" class="block mt-8 mb-2 text-left">ご要望のタイプ<span
                    class="required">*</span></label>
                <select name="function_request_type" id="type" class="w-full rounded-lg">
                  <option hidden>-</option>
                  <option value="0">新機能のリクエスト</option>
                  <option value="1">機能の改善案</option>
                  <option value="2">既存機能のバグ</option>
                  <option value="3">サービス全般</option>
                </select>

                {{-- 件名（お問い合わせのタイトル） --}}
                <label for="title" class="block mt-8 mb-2 text-left">件名（お問い合わせのタイトル）<span
                    class="required">*</span></label>
                <input type="title" id="title" size="50" class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 詳細 --}}
                <label for="detail" class="block mt-8 mb-2 text-left">詳細<span class="required">*</span></label>
                <textarea name="detail" id="detail" cols="30" rows="8"
                  class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                {{-- リクエスト種別 --}}
                <label for="environment" class="block mt-8 mb-2 text-left">ご利用環境<span class="required">*</span></label>
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

                {{-- 参考URL --}}
                <label for="reference_url" class="block mt-8 mb-2 text-left">参考URL</label>
                <input type="reference_url" id="reference_url" size="50"
                  class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 添付ファイル --}}
                <p for="reference_url" class="block mt-8 mb-2 text-left">添付ファイル</p>
                <label for="attached_file_path"
                  class="block w-full py-2 text-center border border-gray-500 rounded-lg cursor-pointer">ファイルを追加</label>
                <input class="hidden" id="attached_file_path" type="file">

                {{-- ボタン --}}
                <div class="mt-10 text-center">
                  <button
                    class="px-24 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                    type="">送信</button>
                </div>
              </li>

              {{-- セキュリティ脆弱性の報告 --}}
              <li class="hidden" id="request_type_2">
                {{-- メールアドレス --}}
                <label for="mail" class="block mt-8 mb-2 text-left">メールアドレス<span class="required">*</span></label>
                <input type="mail" id="mail" size="50" class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 件名（お問い合わせのタイトル） --}}
                <label for="title" class="block mt-8 mb-2 text-left">件名（お問い合わせのタイトル）<span
                    class="required">*</span></label>
                <input type="title" id="title" size="50" class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 詳細 --}}
                <label for="detail" class="block mt-8 mb-2 text-left">詳細<span class="required">*</span></label>
                <textarea name="detail" id="detail" cols="30" rows="8"
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

                {{-- リクエスト種別 --}}
                <label for="environment" class="block mt-8 mb-2 text-left">ご利用環境<span class="required">*</span></label>
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

                {{-- 参考URL --}}
                <label for="reference_url" class="block mt-8 mb-2 text-left">参考URL</label>
                <input type="reference_url" id="reference_url" size="50"
                  class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 添付ファイル --}}
                <p for="reference_url" class="block mt-8 mb-2 text-left">添付ファイル</p>
                <label for="attached_file_path"
                  class="block w-full py-2 text-center border border-gray-500 rounded-lg cursor-pointer">ファイルを追加</label>
                <input class="hidden" id="attached_file_path" type="file">

                {{-- ボタン --}}
                <div class="mt-10 text-center">
                  <button
                    class="px-24 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                    type="">送信</button>
                </div>
              </li>

              {{-- その他お問い合わせ --}}
              <li class="hidden" id="request_type_3">
                {{-- メールアドレス --}}
                <label for="mail" class="block mt-8 mb-2 text-left">メールアドレス<span class="required">*</span></label>
                <input type="mail" id="mail" size="50" class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 件名（お問い合わせのタイトル） --}}
                <label for="title" class="block mt-8 mb-2 text-left">件名（お問い合わせのタイトル）<span
                    class="required">*</span></label>
                <input type="title" id="title" size="50" class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 詳細 --}}
                <label for="detail" class="block mt-8 mb-2 text-left">詳細<span class="required">*</span></label>
                <textarea name="detail" id="detail" cols="30" rows="8"
                  class="w-full py-2 border border-gray-500 rounded-lg"></textarea>

                {{-- リクエスト種別 --}}
                <label for="environment" class="block mt-8 mb-2 text-left">ご利用環境<span class="required">*</span></label>
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

                {{-- 参考URL --}}
                <label for="reference_url" class="block mt-8 mb-2 text-left">参考URL</label>
                <input type="reference_url" id="reference_url" size="50"
                  class="w-full py-2 border border-gray-500 rounded-lg">

                {{-- 添付ファイル --}}
                <p for="reference_url" class="block mt-8 mb-2 text-left">添付ファイル</p>
                <label for="attached_file_path"
                  class="block w-full py-2 text-center border border-gray-500 rounded-lg cursor-pointer">ファイルを追加</label>
                <input class="hidden" id="attached_file_path" type="file">

                {{-- ボタン --}}
                <div class="mt-10 text-center">
                  <button
                    class="px-24 py-3 text-sm font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500"
                    type="">送信</button>
                </div>
              </li>
            </ul>

          </div>

        </div>


      </div>
    </div>
  </div>

</x-app-layout>