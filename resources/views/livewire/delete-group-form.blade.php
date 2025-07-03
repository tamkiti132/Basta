<div x-cloak x-show="$wire.showModal"
    class="flex fixed top-0 left-0 z-30 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
    <div x-on:click.away="$wire.closeModal"
        class="flex flex-col justify-center px-3 py-2 w-full max-w-sm h-auto bg-white rounded-xl">
        <div class="flex flex-col items-center pb-2 mb-6">
            @if($group_data->group_photo_path)
            <div class="object-cover mr-3 w-8 h-8 bg-center rounded-full">
                <img class="object-fill w-8 h-8 rounded-full"
                    src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
            </div>
            @else
            <div class="object-cover w-8 h-8 bg-blue-200 rounded-full"></div>
            @endif
            <p>{{ $group_data->name }}</p>
        </div>

        <div class="flex flex-col gap-6 justify-center mb-6 text-sm font-bold text-center">
            <p class="leading-relaxed">このグループのデータは削除され、<br>
                元に戻すことはできません。</p>
            <p>本当に削除しますか？</p>
        </div>

        <form wire:submit.prevent="deleteGroup" class="flex flex-col gap-2 p-2">
            @csrf

            <x-validation-error name="password" />
            <label for="password">パスワード</label>
            <input type="password" id="password" wire:model='password' class="text-sm rounded-lg sm:text-base">

            <div class="flex gap-4 justify-end pt-2">
                <button class="px-1 py-2 w-28 border border-gray-300 hover:bg-slate-100" type="button"
                    wire:click="closeModal">キャンセル</button>
                <button type="submit"
                    class="px-1 py-2 w-28 text-red-500 border border-red-500 hover:bg-red-50">削除</button>
            </div>
        </form>

    </div>
</div>