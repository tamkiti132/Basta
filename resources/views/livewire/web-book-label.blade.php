<div>
    <button wire:click="toggleLabel('web')" class="flex items-center w-full gap-4 p-2 hover:bg-slate-100">
        @if (in_array('web', $selected_web_book_labels))
        <i class="text-lg sm:text-3xl fas fa-globe fa-fw"></i>
        @else
        <i class="text-lg sm:text-3xl fas fa-globe fa-fw" style="color: #b7bfcd;"></i>
        @endif

        <p class="text-xs sm:text-base">Webサイト</p>
    </button>

    <button wire:click="toggleLabel('book')" class="flex items-center w-full gap-4 p-2 hover:bg-slate-100">
        @if (in_array('book', $selected_web_book_labels))
        <i class="text-lg sm:text-3xl fas fa-book-open fa-fw"></i>
        @else
        <i class="text-lg sm:text-3xl fas fa-book-open fa-fw" style="color: #b7bfcd;"></i>
        @endif
        <p class="text-xs sm:text-base">本</p>
    </button>
</div>