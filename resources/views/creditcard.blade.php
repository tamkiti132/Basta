<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold leading-tight text-gray-800 sm:text-xl">
      クレジットカード
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="grid gap-10 py-24 overflow-hidden bg-white shadow-xl sm:rounded-2xl">

        {{-- カード未登録 --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  {{-- クレジットカードが未登録です --}}
                  <div class="pb-4 mb-10 border-b border-gray-400">
                    <div class="text-lg font-bold text-center">
                      <p>クレジットカードが未登録です</p>
                    </div>
                  </div>
                  {{-- ボタン --}}
                  <div class="text-center mb-7 ">
                    <button
                      class="px-6 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500">カードを登録する</button>
                  </div>

                </div>


              </div>
            </div>
          </div>
        </section>

        {{-- カード情報表示（カード登録済み） --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  {{-- カード情報 --}}
                  <div class="pb-4 mb-10 border-b border-gray-400">
                    <div class="text-lg font-bold text-center">
                      <p>カード情報</p>
                    </div>
                  </div>
                  {{-- クレカのロゴ --}}
                  <div class="flex justify-center w-auto">
                    <img src="/images/card_5brand.png" alt="" class="w-80">
                  </div>

                  {{-- カード情報 --}}
                  <div class="grid grid-cols-2 mt-10">
                    {{-- 左側 --}}
                    <div>
                      {{-- カード番号 --}}
                      <div>
                        <p>カード番号</p>
                        <p>************0000</p>
                      </div>
                    </div>
                    {{-- 右側 --}}
                    <div class="flex justify-center sm:justify-start">
                      {{-- 有効期限 --}}
                      <div>
                        <p>有効期限</p>
                        <p>2023 / 09</p>
                      </div>
                    </div>
                  </div>
                  {{-- カード名義 --}}
                  <div class="mt-10">
                    <p>カード名義</p>
                    <p>ATSUSHI HARADA</p>
                  </div>

                  {{-- ボタン --}}
                  <div class="mt-20 text-center sm:text-left">
                    <div class="mb-7">
                      <button
                        class="px-6 py-3 text-lg font-bold text-white bg-indigo-400 border-0 rounded-2xl focus:outline-none hover:bg-indigo-500">カード情報の変更</button>
                    </div>
                    <div class="mb-7">
                      <button class="font-bold text-red-600 hover:text-red-700">カード情報を削除</button>
                    </div>
                  </div>

                </div>


              </div>
            </div>
          </div>
        </section>

        {{-- カード情報表示（登録） --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  {{-- カード情報 --}}
                  <div class="pb-4 mb-10 border-b border-gray-400">
                    <div class="text-lg font-bold text-center">
                      <p>カード情報</p>
                    </div>
                  </div>
                  {{-- クレカのロゴ --}}
                  <div class="flex justify-center w-auto">
                    <img src="/images/card_5brand.png" alt="" class="w-80">
                  </div>

                  {{-- カード情報 --}}
                  <div class="mt-10 sm:grid sm:grid-cols-2">
                    {{-- 左側 --}}
                    <div>
                      {{-- カード番号 --}}
                      <div>
                        <label for="card_number" class="block text-sm">カード番号<span class="required">*</span></label>
                        <input id="card_number" type="text" class="rounded-lg" size="30"
                          placeholder="例：1111 2222 3333 4444">
                      </div>
                      {{-- カード名義 --}}
                      <div class="mt-10">
                        <label for="card_name" class="block text-sm">カード名義<span class="required">*</span></label>
                        <input id="card_name" type="text" class="rounded-lg" size="30" placeholder="例：TARO YAMADA">
                      </div>
                      {{-- 有効期限 --}}
                      <div class="mt-10">
                        <label for="deadline" class="block text-sm">有効期限<span class="required">*</span></label>
                        <input id="deadline" type="text" class="rounded-lg" size="20" placeholder="月 / 年">
                      </div>
                      {{-- セキュリティコード --}}
                      <div class="mt-10">
                        <label for="security_code" class="block text-sm">セキュリティコード<span
                            class="required">*</span></label>
                        <input id="security_code" type="text" class="rounded-lg" size="20" placeholder="例：123">
                      </div>
                    </div>

                    {{-- 右側 --}}
                    <div class="grid gap-10 pt-10 sm:items-end sm:justify-end sm:grid-cols-2 sm:pt-40">
                      <button
                        class="px-6 py-3 text-lg font-bold border border-black rounded-2xl focus:outline-none hover:bg-gray-200">キャンセル</button>
                      <button
                        class="py-3 text-lg font-bold text-white bg-indigo-400 border-0 px-14 rounded-2xl focus:outline-none hover:bg-indigo-500">保存</button>
                    </div>
                  </div>

                </div>


              </div>
            </div>
          </div>
        </section>

        {{-- カード情報表示（変更） --}}
        <section class="text-gray-600 body-font">
          <div class="container px-5 mx-auto">
            <div class="-m-4 ">
              <div class="p-4">
                <div class="relative px-8 pt-8 pb-8 overflow-hidden bg-gray-100 bg-opacity-75 shadow-md rounded-2xl">
                  {{-- カード情報 --}}
                  <div class="pb-4 mb-10 border-b border-gray-400">
                    <div class="text-lg font-bold text-center">
                      <p>カード情報</p>
                    </div>
                  </div>
                  {{-- クレカのロゴ --}}
                  <div class="flex justify-center w-auto">
                    <img src="/images/card_5brand.png" alt="" class="w-80">
                  </div>

                  {{-- カード情報 --}}
                  <div class="mt-10 sm:grid sm:grid-cols-2">
                    {{-- 左側 --}}
                    <div>
                      {{-- カード番号 --}}
                      <div>
                        <label for="card_number" class="block text-sm">カード番号<span class="required">*</span></label>
                        <input id="card_number" type="text" class="rounded-lg" size="30"
                          placeholder="例：1111 2222 3333 4444">
                      </div>
                      {{-- カード名義 --}}
                      <div class="mt-10">
                        <label for="card_name" class="block text-sm">カード名義<span class="required">*</span></label>
                        <input id="card_name" type="text" class="rounded-lg" size="30" placeholder="">
                      </div>
                      {{-- 有効期限 --}}
                      <div class="mt-10">
                        <label for="deadline" class="block text-sm">有効期限<span class="required">*</span></label>
                        <input id="deadline" type="text" class="rounded-lg" size="20" placeholder="">
                      </div>
                      {{-- セキュリティコード --}}
                      <div class="mt-10">
                        <label for="security_code" class="block text-sm">セキュリティコード<span
                            class="required">*</span></label>
                        <input id="security_code" type="text" class="rounded-lg" size="20" placeholder="例：123">
                      </div>
                    </div>

                    {{-- 右側 --}}
                    <div class="grid gap-10 pt-10 sm:items-end sm:justify-end sm:grid-cols-2 sm:pt-40">
                      <button
                        class="px-6 py-3 text-lg font-bold border border-black rounded-2xl focus:outline-none hover:bg-gray-200">キャンセル</button>
                      <button
                        class="py-3 text-lg font-bold text-white bg-indigo-400 border-0 px-14 rounded-2xl focus:outline-none hover:bg-indigo-500">保存</button>
                    </div>
                  </div>

                </div>


              </div>
            </div>
          </div>
        </section>

      </div>
    </div>
  </div>

</x-app-layout>