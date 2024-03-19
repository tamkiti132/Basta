<div class="flex flex-col p-2 text-xs">
    @foreach ($labels as $label)
    <div class="flex items-center w-full space-x-2">
        <input type="checkbox" wire:model="checked.{{ $label->id }}" id="checkbox{{$label->id}}" class="mr-3">
        <label for="checkbox{{$label->id}}"
            class="flex-auto inline-block p-2 bg-transparent cursor-pointer hover:bg-slate-100"
            wire:click="$set('checked.{{ $label->id }}', !checked.{{ $label->id }})">{{$label->name}}</label>
    </div>
    @endforeach

    {{-- ページに①通常アクセス or ②「戻るボタン」でアクセス　した際、 １：input（text）２：selectbox ３：textarea ４：checkbox ５：radiobutton をリセットするための処理
    --}}
    <script>
        function resetFormElements() {
            const selectElement = document.querySelector('select.max-w-xs');
            const inputElements = document.querySelectorAll('input:not([name="_token"])');
            const textareaElements = document.querySelectorAll('textarea');
            
            if (selectElement) {
                selectElement.value = '';
            }
            inputElements.forEach(input => {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
            });
            textareaElements.forEach(textarea => {
                textarea.value = '';
            });
        }
        
        window.addEventListener('load', resetFormElements);
    </script>

</div>