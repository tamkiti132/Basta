<div>
    {{-- 退会確認モーダル --}}
    <div x-data="{
        showMemberQuitModal: @entangle('showMemberQuitModal'),
    }">
        <div x-cloak x-show="showMemberQuitModal"
            class="flex fixed top-0 left-0 z-30 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
            <form wire:submit.prevent="quitGroup" x-on:click.away="$wire.closeModal"
                class="flex flex-col justify-center px-3 py-2 w-full max-w-sm h-auto bg-white rounded-xl">
                @csrf

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
                    <input type="password" id="password" name="password" wire:model='password'
                        class="text-sm rounded-lg sm:text-base">

                    <div class="flex gap-4 justify-end pt-2">
                        <button class="px-1 py-2 w-28 border border-gray-300 hover:bg-slate-100" type="button"
                            x-on:click="$wire.closeModal">キャンセル</button>

                        <button type="submit"
                            class="px-1 py-2 w-28 text-red-500 border border-red-500 hover:bg-red-50">退会させる</button>
                    </div>
                </div>

            </form>
        </div>


    </div>
</div>