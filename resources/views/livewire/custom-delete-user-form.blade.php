<x-action-section>
    <x-slot name="title">
        {{ __('Delete Account') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Permanently delete your account.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('アカウントが削除されると、そのリソースとデータはすべて完全に削除されます。アカウントを削除する前に、保持したいデータや情報をダウンロードしてください。') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Delete Account') }}
            </x-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model="confirmingUserDeletion">
            <x-slot name="title">
                {{ __('Delete Account') }}
            </x-slot>

            <x-slot name="content">
                {{ __('アカウントを削除してもよろしいですか?アカウントが削除されると、そのリソースとデータはすべて完全に削除されます。アカウントを完全に削除することを確認するため、パスワードを入力してください。')
                }}

                <div class="mt-4" x-data="{}"
                    x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    {{-- パスワードが間違っている場合は、エラーメッセージを表示する --}}
                    <x-flash-message status="error" width="w-full" />

                    <x-input-error for="password" class="mt-2" />

                    <x-input type="password" class="block w-3/4 mt-1" autocomplete="current-password"
                        placeholder="{{ __('Password') }}" x-ref="password" wire:model.defer="password"
                        wire:keydown.enter="isManager" />

                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-3" wire:click="isManager" wire:loading.attr="disabled">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>