<div class="inline">
    @if($memo->laterReads()->where('user_id', Auth::id())->exists())
    <button wire:click="toggleLaterRead">
        <i class="inline sm:text-lg fa-solid fa-file"></i>
    </button>
    @else
    <button wire:click="toggleLaterRead">
        <i class="inline sm:text-lg fa-regular fa-file"></i>
    </button>
    @endif
    <span class="ml-1">{{ $memo->laterReads->count() }}</span>
</div>