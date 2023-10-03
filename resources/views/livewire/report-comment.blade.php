<div x-data="{
    showModalReportComment: @entangle('showModalReportComment'),
}">
    <div x-cloak x-show="showModalReportComment"
        class="fixed top-0 left-0 z-30 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
        <div x-on:click.away="$wire.closeModalReportComment"
            class="flex flex-col w-full h-auto max-w-md px-3 py-2 bg-white rounded-xl">
            <div class="pb-2 mb-4 font-bold text-center border-b border-black">
                <p>問題点を教えてください</p>
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
                    class="rounded-xl"></textarea>

                <div class="flex justify-end gap-4 pt-2">
                    <button type="button" class="px-1 py-2 border border-gray-300 w-28 hover:bg-slate-100"
                        x-on:click="$wire.closeModalReportComment">キャンセル</button>
                    <button type="submit"
                        class="px-1 py-2 text-red-500 border border-red-500 w-28 hover:bg-red-50">通報</button>
                </div>
            </form>

        </div>
    </div>
</div>