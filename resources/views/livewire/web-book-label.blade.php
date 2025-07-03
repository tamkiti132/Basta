<div>
    <button wire:click="toggleLabel('web')" class="flex gap-4 items-center p-2 w-full hover:bg-slate-100">
        @if (in_array('web', $selected_web_book_labels))
        <i class="text-lg sm:text-xl fas fa-globe fa-fw"></i>
        @else
        <i class="text-lg sm:text-xl fas fa-globe fa-fw" style="color: #b7bfcd;"></i>
        @endif

        <p class="text-xs sm:text-sm">Webサイト</p>
    </button>

    <button wire:click="toggleLabel('book')" class="flex gap-4 items-center p-2 w-full hover:bg-slate-100">
        @if (in_array('book', $selected_web_book_labels))
        <i class="text-lg sm:text-xl fas fa-book-open fa-fw"></i>
        @else
        <i class="text-lg sm:text-xl fas fa-book-open fa-fw" style="color: #b7bfcd;"></i>
        @endif
        <p class="text-xs sm:text-sm">本</p>
    </button>
</div>