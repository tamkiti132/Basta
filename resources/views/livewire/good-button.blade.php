<div class="inline">
    @if($isGood)
    <button wire:click="toggleGood">
        <i class="text-sm text-pink-300 fa-solid fa-regular fa-heart"></i>
    </button>
    @else
    <button wire:click="toggleGood">
        <i class="text-sm fa-regular fa-heart"></i>
    </button>
    @endif
    <span class="ml-1">{{ $memo->goods->count() }}</span>
</div>
