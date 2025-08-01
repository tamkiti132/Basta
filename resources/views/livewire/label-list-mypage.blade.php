<div>
    @if($group_id)
    @foreach ($labels as $label)
    <button wire:key="{{ $label->id }}" wire:click="toggleLabel({{ $label->id }})"
        class="flex items-center w-full gap-4 p-2 hover:bg-slate-100 {{ in_array($label->id, $selected_labels) ? 'selected' : '' }}">
        @if (in_array($label->id, $selected_labels))
        <span class="text-2xl material-symbols-rounded" style="font-variation-settings:'FILL' 1">label</span>
        @else
        <span class="text-2xl material-symbols-rounded">label</span>
        @endif
        <p class="text-xs text-left whitespace-normal break-all sm:text-sm">{{ $label->name }}</p>
    </button>
    @endforeach
    @endif
</div>