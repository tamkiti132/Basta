<div class="inline">
    @if($memo->goods()->where('user_id', Auth::id())->exists())
    <button wire:click="toggleGood">
        <i class="text-pink-300 sm:text-lg fa-solid fa-regular fa-heart"></i>
    </button>
    @else
    <button wire:click="toggleGood">
        <i class="sm:text-lg fa-regular fa-heart"></i>
    </button>
    @endif
    <span class="ml-1">{{ $memo->goods->count() }}</span>
</div>