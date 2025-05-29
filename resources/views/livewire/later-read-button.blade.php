<div class="inline">
    @if($isLaterRead)
    <button wire:click="toggleLaterRead">
        <i class="inline text-sm fa-solid fa-file"></i>
    </button>
    @else
    <button wire:click="toggleLaterRead">
        <i class="inline text-sm fa-regular fa-file"></i>
    </button>
    @endif
    <span class="ml-1">{{ $memo->laterReads->count() }}</span>
</div>
