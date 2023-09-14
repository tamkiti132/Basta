<div class="flex flex-col p-2 text-xs sm:text-base">
    @foreach ($labels as $label)
    <div class="flex items-center w-full space-x-2">
        <input type="checkbox" wire:model="checked.{{ $label->id }}" id="checkbox{{$label->id}}" class="mr-3">
        <label for="checkbox{{$label->id}}"
            class="flex-auto inline-block p-2 bg-transparent cursor-pointer hover:bg-slate-100"
            wire:click="$set('checked.{{ $label->id }}', !checked.{{ $label->id }})">{{$label->name}}</label>
    </div>
    @endforeach
</div>