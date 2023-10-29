{{-- 退会確認モーダル --}}
<div x-data="{
    showModal: @entangle('showModal'),
    showNextManagerModal: @entangle('showNextManagerModal'),
}">
    <div x-cloak x-show="showModal"
        class="fixed top-0 left-0 z-30 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
        <form wire:submit.prevent="quitGroup" x-on:click.away="$wire.closeModal"
            class="flex flex-col justify-center w-full h-auto max-w-sm px-3 py-2 bg-white rounded-xl">
            @csrf

            <div class="flex flex-col items-center pb-2 mb-6">
                @if($group_data->group_photo_path)
                <div class="object-cover w-8 h-8 mr-3 bg-center rounded-full">
                    <img class="object-fill w-8 h-8 rounded-full"
                        src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                </div>
                @else
                <div class="object-cover w-8 h-8 bg-blue-200 rounded-full"></div>
                @endif
                <p>{{ $group_data->name }}</p>
            </div>

            <div class="flex justify-center mb-6 text-sm font-bold text-center">
                <p class="leading-relaxed">本当に退会しますか？<br>
                    （グループ内で投稿したメモ、コメントは残ります）</p>
            </div>

            <div action="" method="GET" class="flex flex-col gap-2 p-2">

                <x-validation-error name="password" />
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" wire:model='password'
                    class="text-sm rounded-lg sm:text-base">

                <div class="flex justify-end gap-4 pt-2">
                    <button class="px-1 py-2 border border-gray-300 w-28 hover:bg-slate-100" type="button"
                        x-on:click="$wire.closeModal">キャンセル</button>

                    <button type="submit"
                        class="px-1 py-2 text-red-500 border border-red-500 w-28 hover:bg-red-50">退会</button>
                </div>
            </div>

        </form>
    </div>

    {{-- 次の管理者選択モーダル --}}

    <div x-cloak x-show="showNextManagerModal"
        class="fixed top-0 left-0 z-40 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
        <div class="flex flex-col justify-center w-full h-auto max-w-xl px-3 py-2 bg-white rounded-xl"
            x-on:click.away="$wire.closeModal">
            <div class="flex flex-col items-center pb-2 mb-6">
                @if($group_data->group_photo_path)
                <div class="object-cover w-8 h-8 mr-3 bg-center rounded-full">
                    <img class="object-fill w-8 h-8 rounded-full"
                        src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                </div>
                @else
                <div class="object-cover w-8 h-8 mr-3 bg-blue-200 bg-center rounded-full"></div>
                @endif
                <p>{{ $group_data->name }}</p>
            </div>

            <div class="flex justify-center mb-6 text-sm font-bold text-center">
                <p class="leading-relaxed">サブ管理者の中から、<br>
                    次の管理者を選択してください</p>
            </div>


            <form wire:submit.prevent="quitGroupForManager" class="flex flex-col p-2">
                <select class="w-full p-2 mb-4 border border-gray-300 rounded" name="users" wire:model.defer="user_id">
                    @foreach ($group_data->userRoles as $user_data)
                    <option value="{{ $user_data->id }}">
                        {{ $user_data->nickname }} ( {{ $user_data->username }} )
                    </option>
                    @endforeach
                </select>

                <div class="flex justify-end gap-4 pt-2">
                    <button class="px-1 py-2 border border-gray-300 w-28 hover:bg-slate-100"
                        x-on:click="$wire.closeModal">キャンセル</button>
                    <button type="submit"
                        class="px-1 py-2 text-red-500 border border-red-500 w-28 hover:bg-red-50">退会</button>
                </div>
            </form>

        </div>
    </div>

</div>