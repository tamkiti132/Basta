{{-- 退会確認モーダル --}}
<div x-data="{
    showModal: @entangle('showModal'),
    showNextManagerModal: @entangle('showNextManagerModal'),
    showModalNobodySubManager: @entangle('showModalNobodySubManager'),
    showModalFinalConfirmation: @entangle('showModalFinalConfirmation'),
}">
    <div x-cloak x-show="showModal"
        class="flex fixed top-0 left-0 z-30 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
        <form wire:submit.prevent="quitGroup" x-on:click.away="$wire.closeModal"
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

            <div class="flex justify-center mb-6 text-sm font-bold text-center">
                <p class="leading-relaxed">本当に退会しますか？<br>
                    （グループ内で投稿したメモ、コメントは残ります）</p>
            </div>

            <div class="flex flex-col gap-2 p-2">

                <x-validation-error name="password" />
                <label for="password">パスワード</label>
                <input type="password" id="password" wire:model='password' class="text-sm rounded-lg sm:text-base">

                <div class="flex gap-4 justify-end pt-2">
                    <button type="button" class="px-1 py-2 w-28 border border-gray-300 hover:bg-slate-100"
                        x-on:click="$wire.closeModal">キャンセル</button>

                    <button type="submit"
                        class="px-1 py-2 w-28 text-red-500 border border-red-500 hover:bg-red-50">退会</button>
                </div>
            </div>

        </form>
    </div>

    {{-- 次の管理者選択モーダル --}}
    <div x-cloak x-show="showNextManagerModal"
        class="flex fixed top-0 left-0 z-40 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
        <div class="flex flex-col justify-center px-3 py-2 w-full max-w-xl h-auto bg-white rounded-xl"
            x-on:click.away="$wire.closeModal">
            <div class="flex flex-col items-center pb-2 mb-6">
                @if($group_data->group_photo_path)
                <div class="object-cover mr-3 w-8 h-8 bg-center rounded-full">
                    <img class="object-fill w-8 h-8 rounded-full"
                        src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                </div>
                @else
                <div class="object-cover mr-3 w-8 h-8 bg-blue-200 bg-center rounded-full"></div>
                @endif
                <p>{{ $group_data->name }}</p>
            </div>

            <div class="flex justify-center mb-6 text-sm font-bold text-center">
                <p class="leading-relaxed">サブ管理者の中から、<br>
                    次の管理者を選択してください</p>
            </div>

            <form wire:submit.prevent="quitGroupForManager" class="flex flex-col p-2">
                <select class="p-2 mb-4 w-full rounded border border-gray-300" wire:model.defer="selectedUserId">
                    <option value="">次の管理者を選択してください</option>
                    @foreach ($group_data->userRoles as $user_data)
                    <option value="{{ $user_data->id }}" wire:key="user_role_option_{{ $user_data->id }}">
                        {{ $user_data->nickname }} ( {{ $user_data->username }} )
                    </option>
                    @endforeach
                </select>

                <div class="flex gap-4 justify-end pt-2">
                    <button type="button" class="px-1 py-2 w-28 border border-gray-300 hover:bg-slate-100"
                        x-on:click="$wire.closeModal">キャンセル</button>
                    <button type="submit"
                        class="px-1 py-2 w-28 text-red-500 border border-red-500 hover:bg-red-50">退会</button>
                </div>
            </form>
        </div>
    </div>

    {{-- サブ管理者を選択しない時の最終確認モーダル --}}
    <div x-cloak x-show="showModalFinalConfirmation"
        class="flex fixed top-0 left-0 z-30 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
        <form wire:submit.prevent="deleteGroup" x-on:click.away="$wire.closeModal"
            class="flex flex-col justify-center px-3 py-2 w-full max-w-sm h-auto bg-white rounded-xl">
            <div class="flex flex-col items-center pb-2 mb-6">
                @if($group_data->group_photo_path)
                <div class="object-cover mr-3 w-8 h-8 bg-center rounded-full">
                    <img class="object-fill w-8 h-8 rounded-full"
                        src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                </div>
                @else
                <div class="object-cover mr-3 w-8 h-8 bg-blue-200 bg-center rounded-full"></div>
                @endif
                <p>{{ $group_data->name }}</p>
            </div>

            <div class="flex justify-center mb-6 text-sm font-bold text-center">
                <p class="leading-relaxed">次の管理者を選択しなくてよいのですか？<br>
                    次の管理者を選択しない場合、<br>
                    このグループは削除され、<br>
                    元に戻すことはできません。</p>
            </div>

            <div class="flex flex-col gap-2 p-2">

                <x-validation-error name="password" />
                <label for="password2">パスワード</label>
                <input type="password" id="password2" wire:model='password' class="text-sm rounded-lg sm:text-base">

                <div class="flex flex-col gap-5 justify-end items-center pt-2 text-sm sm:flex-row sm:gap-2">
                    <button type="button" class="px-1 py-2 w-28 border border-gray-300 hover:bg-slate-100"
                        x-on:click="$wire.closeModal">キャンセル</button>
                    <button type="submit"
                        class="px-1 py-2 w-28 text-red-500 border border-red-500 hover:bg-red-50">削除</button>
                </div>
            </div>

        </form>
    </div>

    {{-- サブ管理者いませんよモーダル --}}
    <div x-cloak x-show="showModalNobodySubManager"
        class="flex fixed top-0 left-0 z-30 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
        <form wire:submit.prevent="quitGroupWhenNobodySubManager" x-on:click.away="$wire.closeModal"
            class="flex flex-col justify-center px-3 py-2 w-full max-w-sm h-auto bg-white rounded-xl">
            <div class="flex flex-col items-center pb-2 mb-6">
                @if($group_data->group_photo_path)
                <div class="object-cover mr-3 w-8 h-8 bg-center rounded-full">
                    <img class="object-fill w-8 h-8 rounded-full"
                        src="{{ asset('storage/group-image/'. $group_data->group_photo_path) }}" />
                </div>
                @else
                <div class="object-cover mr-3 w-8 h-8 bg-blue-200 bg-center rounded-full"></div>
                @endif
                <p>{{ $group_data->name }}</p>
            </div>

            <div class="flex justify-center mb-6 text-sm font-bold text-center">
                <p class="leading-relaxed">サブ管理者がいません。<br>
                    管理者権限を継承するために、<br>
                    サブ管理者を設定してください。<br>
                    （サブ管理者の中から、時期管理者を選択するため）<br>
                    <br>
                    管理者権限を継承しなかった場合、<br>
                    このグループは削除され、<br>
                    元に戻すことはできません。
                </p>
            </div>

            <div class="flex flex-col gap-2 p-2">

                <x-validation-error name="password" />
                <label for="password3">パスワード</label>
                <input type="password" id="password3" wire:model='password' class="text-sm rounded-lg sm:text-base">

                <div class="flex flex-col gap-5 justify-center items-center pt-2 text-sm sm:flex-row sm:gap-2">
                    <button class="px-1 py-2 w-48 text-red-500 border border-red-500 hover:bg-red-50"
                        x-on:click="">サブ管理者を設定しない</button>
                    <button type="button" class="px-1 py-2 w-48 border border-gray-300 hover:bg-slate-100"
                        onclick="location.href='{{ route('group.member_edit', ['group_id' => $group_data->id]) }}' ">サブ管理者を設定する</button>
                </div>
            </div>

        </form>
    </div>

</div>