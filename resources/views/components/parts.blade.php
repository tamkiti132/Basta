{{-- サイドバー（横からスクロールするタイプ） ＊transformでの実装　--}}
{{-- ラベル表示（左側） --}}
<div class="absolute z-20 col-span-2 xl:static sm:block">
  <input type="checkbox" id="drawer-toggle" class="sr-only peer" checked>
  <label for="drawer-toggle"
    class="left-0 inline-block p-2 transition-all duration-500 bg-indigo-500 rounded-lg xl:hidden peer-checked: top-40 ">
    <div class="w-6 h-1 mb-3 bg-white rounded-lg"></div>
    <div class="w-6 h-1 bg-white rounded-lg"></div>
  </label>
  <div
    class="h-full transition-all duration-500 transform -translate-x-full bg-white shadow-lg rounded-r-2xl peer-checked:translate-x-0">
    <div class="z-20 px-4 py-4 sm:block">
      {{-- ラベル表示 --}}
      <div class="xl:col-span-2">
        <button class="flex items-center gap-4 mb-5">
          <i class="text-lg sm:text-3xl fas fa-globe fa-fw"></i>
          <p class="text-sm sm:text-base">Webサイト</p>
        </button>
        <button class="flex items-center gap-4 mb-5">
          <i class="text-lg sm:text-3xl fas fa-book-open fa-fw"></i>
          <p class="text-sm sm:text-base">本</p>
        </button>
        <button class="flex items-center gap-3 mb-5">
          <span class="sm:text-4xl material-symbols-rounded" style="font-variation-settings:'FILL' 1">label</span>
          <p class="text-sm sm:text-base">プログラミング</p>
        </button>
        <button class="flex items-center gap-3 mb-5">
          <span class="sm:text-4xl material-symbols-rounded" style="font-variation-settings:'FILL' 1">label</span>
          <p class="text-sm sm:text-base">設計</p>
        </button>
        <button class="flex items-center gap-3 mb-5">
          <span class="sm:text-4xl material-symbols-rounded">label</span>
          <p class="text-sm sm:text-base">Laravel</p>
        </button>
        <button class="flex items-center gap-4 mb-5">
          <i class="sm:text-2xl fa-solid fa-pencil fa-fw"></i>
          <p class="text-sm sm:text-base">Laravel</p>
        </button>
      </div>
    </div>
  </div>
</div>


{{-- サイドバー（横からスクロールするタイプ） ＊alpineでの実装（1.transform使ってない。 2.x-dataで囲まれてる部分の範囲外の要素は制御しきれない。）--}}
{{-- ラベル表示（左側） --}}
<div x-data="{open: false}">
  <div>
    <button x-on:click="open = true" class="flex items-center gap-4 mb-5"><i
        class="sm:text-2xl fa-solid fa-pencil fa-fw"></i>
      <p class="text-sm sm:text-base">ラベルを編集</p>
    </button>
  </div>

  {{-- ラベル編集モーダル --}}
  <div x-cloak x-show="open"
    class="fixed top-0 left-0 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
    <div x-on:click.away="open = false" class="flex flex-col w-full h-auto max-w-xs px-3 py-2 bg-white rounded-xl">
      <div class="flex items-end justify-between pb-2 mb-2 border-b border-black">
        <p>こんにチワワ</p>
        <div x-on:click="open = false" class="text-2xl cursor-pointer">+</div>
      </div>
      <div class="flex flex-col py-2">
        {{-- ラベル1個分 --}}
        <div class="flex justify-between">
          <button class="w-full py-2 text-left">
            <p class="text-xs sm:text-base">プログラミング</p>
          </button>
          <button class="px-2">
            <i class="fa-regular fa-trash-can"></i>
          </button>
        </div>
        {{-- ラベル1個分 --}}
        <div class="flex justify-between">
          <button class="w-full py-2 text-left">
            <p class="text-xs sm:text-base">設計</p>
          </button>
          <button class="px-2">
            <i class="fa-regular fa-trash-can"></i>
          </button>
        </div>
        {{-- ラベル1個分 --}}
        <div class="flex justify-between">
          <button class="w-full py-2 text-left">
            <p class="text-xs sm:text-base">Laravel</p>
          </button>
          <button class="px-2">
            <i class="fa-regular fa-trash-can"></i>
          </button>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- ラベルのデザイン --}}
{{-- ラベル（チェック済み） --}}
<button class="flex items-center w-full gap-4 p-2 hover:bg-slate-100">
  <span class="sm:text-4xl material-symbols-rounded" style="font-variation-settings:'FILL' 1">label</span>
  <p class="text-xs sm:text-base">設計</p>
</button>
{{-- ラベル（チェックなし） --}}
<button class="flex items-center w-full gap-4 p-2 hover:bg-slate-100">
  <span class="sm:text-4xl material-symbols-rounded">label</span>
  <p class="text-xs sm:text-base">Laravel</p>
</button>