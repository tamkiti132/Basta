<div x-data="{
    showModalReportMemo: @entangle('showModalReportMemo'),
}" x-init="
    $watch('showModalReportMemo', value => {
        if (value) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    });
">
    <div x-cloak x-show="showModalReportMemo"
        class="flex fixed top-0 left-0 z-30 justify-center items-center w-screen h-screen bg-black bg-opacity-40 border">
        <div x-on:click.away="$wire.closeModalReportMemo"
            class="flex flex-col px-3 py-2 w-full max-w-md h-auto bg-white rounded-xl">
            <div class="pb-2 mb-4 font-bold text-center border-b border-black">
                <p>問題点を教えてください</p>
            </div>

            <div x-data="{ showMessage: false, message: '' }"
                @flash-message.window="showMessage = true; message = $event.detail.message; setTimeout(() => showMessage = false, 4000);">
                <div x-show="showMessage" x-cloak
                    class="p-2 mx-auto w-1/2 font-bold text-center text-white bg-blue-300 rounded-2xl">
                    <span x-text="message"></span>
                </div>
            </div>

            <form wire:submit.prevent="createReport" class="flex flex-col gap-2 p-2">

                <x-validation-error name="reason" />

                <div class="p-2 hover:bg-slate-100">
                    <label class="block">
                        <input type="radio" wire:model.defer="reason" name="reason" value="1" class="mr-3">
                        <span>法律違反</span>
                    </label>
                </div>
                <div class="p-2 hover:bg-slate-100">
                    <label class="block">
                        <input type="radio" wire:model.defer="reason" name="reason" value="2" class="mr-3">
                        <span>不適切なコンテンツ</span>
                    </label>
                </div>
                <div class="p-2 hover:bg-slate-100">
                    <label class="block">
                        <input type="radio" wire:model.defer="reason" name="reason" value="3" class="mr-3">
                        <span>フィッシング or スパム</span>
                    </label>
                </div>
                <div class="p-2 hover:bg-slate-100">
                    <label class="block">
                        <input type="radio" wire:model.defer="reason" name="reason" value="4" class="mr-3">
                        <span>その他</span>
                    </label>
                </div>


                <x-validation-error name="detail" />

                <label for="detail">問題がある点の詳細</label>
                <textarea name="detail" id="detail" cols="30" rows="5" wire:model.defer="detail"
                    class="overflow-auto max-h-36 text-xs rounded-xl sm:text-sm"></textarea>

                <div class="flex gap-4 justify-end pt-2">
                    <button type="button" class="px-1 py-2 w-28 border border-gray-300 hover:bg-slate-100"
                        x-on:click="$wire.closeModalReportMemo">キャンセル</button>
                    <button type="submit"
                        class="px-1 py-2 w-28 text-red-500 border border-red-500 hover:bg-red-50">通報</button>
                </div>
            </form>

        </div>
    </div>
</div>