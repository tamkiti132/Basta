<div x-data="{
    showLabelEditModal: @entangle('showLabelEditModal'),
    isComposing: false
}" x-init="
    $watch('showLabelEditModal', value => {
        if (value) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    });
">
    <div x-cloak x-show="showLabelEditModal"
        class="flex fixed top-0 left-0 justify-center items-center w-screen h-screen text-xs bg-black bg-opacity-40 border">
        <div x-on:click.away="$wire.closeLabelEditModal"
            class="flex flex-col px-3 py-2 w-full max-w-sm h-auto bg-white rounded-xl md:max-w-md">
            <x-validation-errors class="mb-4 text-xs" />
            <div class="flex justify-between items-center">
                <input type="text" name="label" class="w-10/12 text-sm rounded-lg" wire:model="labelName"
                    x-on:compositionstart="isComposing = true" x-on:compositionend="isComposing = false"
                    x-on:keydown.enter="if (!isComposing) $wire.createLabel()" />
                <button x-on:click="$wire.createLabel()"
                    class="w-1/12 text-xl text-center text-gray-600 cursor-pointer">
                    &#10003;</button>
            </div>
            <div class="pb-6 mb-2 border-b border-black"></div>
            <div class="flex overflow-auto flex-col py-2 max-h-80">
                @foreach ($labels as $label)
                <div class="flex justify-between">
                    <input type="text" wire:model.defer="labelNames.{{ $label->id }}"
                        x-on:compositionstart="isComposing = true" x-on:compositionend="isComposing = false"
                        x-on:keydown.enter="if (!isComposing) $wire.updateLabel({{ $label->id }}, $event.target.value)"
                        class="py-4 w-full text-xs text-left border-none hover:bg-slate-100">
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