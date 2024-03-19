<div x-data="{
    showLabelEditModal: @entangle('showLabelEditModal'),
    isComposing: false
}">
    <div x-cloak x-show="showLabelEditModal"
        class="fixed top-0 left-0 flex items-center justify-center w-screen h-screen text-xs bg-black border bg-opacity-40">
        <div x-on:click.away="$wire.closeLabelEditModal"
            class="flex flex-col w-full h-auto max-w-sm px-3 py-2 bg-white md:max-w-md rounded-xl">
            <x-validation-errors class="mb-4 text-xs" />
            <div class="flex items-center justify-between">
                <input type="text" name="label" class="w-10/12 text-sm rounded-lg" wire:model="labelName"
                    x-on:compositionstart="isComposing = true" x-on:compositionend="isComposing = false"
                    x-on:keydown.enter="if (!isComposing) $wire.createLabel()" />
                <button x-on:click="$wire.createLabel()"
                    class="w-1/12 text-xl text-center text-gray-600 cursor-pointer">
                    &#10003;</button>

            </div>

            <div class="pb-6 mb-2 border-b border-black"></div>

            <div class="flex flex-col py-2">
                @foreach ($labels as $label)
                <div class="flex justify-between">
                    <input type="text" wire:model.defer="labelNames.{{ $label->id }}"
                        x-on:compositionstart="isComposing = true" x-on:compositionend="isComposing = false"
                        x-on:keydown.enter="if (!isComposing) $wire.updateLabel({{ $label->id }}, $event.target.value)"
                        class="w-full py-4 text-xs text-left border-none hover:bg-slate-100">

                    <button class="px-2 hover:bg-slate-100" onclick="confirmDeletion({{ $label->id }})">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>

                </div>
                @endforeach

            </div>
        </div>
    </div>
    <script>
        function confirmDeletion(labelId) {
                        if (confirm('このラベルは削除され、\nすべてのメモからもこのラベルが削除されます。\nこのラベルを削除してもよろしいですか？')) {
                            Livewire.emit('deleteLabel', labelId);
                        }
                    }
    </script>
</div>