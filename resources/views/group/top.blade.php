<x-app-layout>


  {{-- <div class="pt-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <form method="get" action="{{ route('group.index', ['group_id' => $group_data['id']]) }}" class="text-left">
      <input type="text" name="search" placeholder="タイトルかメモ概要のワードで検索" class="rounded-xl" size="50">
      <button class="px-3 py-2 font-bold">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
    </form>
  </div> --}}

  @livewire('memo-list', ['group_id' => session()->get('group_id')])

  {{-- <div class="flex justify-center">
    {{ $all_memos_data_paginated->links() }}
  </div> --}}

  {{-- <script>
    document.addEventListener('DOMContentLoaded', (event) => {
                let webBtn = document.getElementById('web-btn');
                let bookBtn = document.getElementById('book-btn');
                
                function toggleParam(param) {
                let url = new URL(window.location.href);
                
                if (!url.searchParams.has('web') && !url.searchParams.has('book')) {
                url.searchParams.set('web', param === 'web' ? 'false' : 'true');
                url.searchParams.set('book', param === 'book' ? 'false' : 'true');
                } else {
                url.searchParams.set(param, url.searchParams.get(param) === "true" ? "false" : "true");
                }
                
                window.location.href = url.href;
                }
                
                webBtn.addEventListener('click', () => toggleParam('web'));
                bookBtn.addEventListener('click', () => toggleParam('book'));
                });
  </script> --}}

</x-app-layout>