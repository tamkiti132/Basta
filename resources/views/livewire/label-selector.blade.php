<div class="flex flex-col p-2 text-xs" wire:init="$refresh">
    @foreach ($labels as $label)
    <div class="flex items-center space-x-2 w-full">
        <input type="checkbox" wire:model="checked.{{ $label->id }}" id="checkbox{{$label->id}}" class="mr-3">
        <label for="checkbox{{$label->id}}"
            class="inline-block flex-auto p-2 bg-transparent cursor-pointer hover:bg-slate-100"
            wire:click="$set('checked.{{ $label->id }}', !checked.{{ $label->id }})">{{$label->name}}</label>
    </div>
    @endforeach
</div>