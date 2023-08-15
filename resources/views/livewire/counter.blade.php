<div>
    <!-- Alpine counterコンポーネント -->
    <div x-data>
        <h1 x-text="$wire.count"></h1>

        <button x-on:click="$wire.increment()">Increment</button>
    </div>
</div>



<div x-cloak x-show="$wire.showModal"
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
            <input type="password" id="password" wire:model='password' class="text-sm rounded-lg sm:text-base">

            <div class="flex justify-end gap-4 pt-2">
                <button class="px-1 py-2 border border-gray-300 w-28 hover:bg-slate-100" type="button"
                    wire:click="closeModal">キャンセル</button>

                @can('manager', $group_data)
                <button type="submit"
                    class="px-1 py-2 text-red-500 border border-red-500 w-28 hover:bg-red-50">退会</button>
                @else
                <button type="submit"
                    class="px-1 py-2 text-red-500 border border-red-500 w-28 hover:bg-red-50">退会</button>
                @endcan
            </div>
        </div>

    </form>
</div>

<div x-cloak x-show="$wire.showNextManagerModal"
    class="fixed top-0 left-0 z-20 flex items-center justify-center w-screen h-screen bg-black border bg-opacity-40">
    <div x-on:click.away="$wire.closeModal"
        class="flex flex-col justify-center w-full h-auto max-w-xl px-3 py-2 bg-white rounded-xl">
        <div class="flex flex-col items-center pb-2 mb-6">
            <div class="object-cover w-8 h-8 bg-blue-200 rounded-full"></div>
            <p>グループ名</p>
        </div>

        <div class="flex justify-center mb-6 text-sm font-bold text-center">
            <p class="leading-relaxed">サブ管理者の中から、<br>
                次の管理者を選択してください</p>
        </div>

        <form wire:submit.prevent="quitGroup" class="flex flex-col p-2">


            <button class="items-center py-2 text-xs sm:grid sm:grid-cols-7 hover:bg-slate-100">

                <div class="flex items-center sm:col-span-1">
                    <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                </div>
                <div class="col-span-3 text-left">
                    <p>はらーだ</p>
                </div>

                <div class="ml-16 text-left sm:mt-0 sm:ml-0 sm:col-span-3">
                    <p class="text-gray-500">
                        @haruemfajfjoafoafof
                    </p>
                </div>
            </button>

            <button class="items-center py-2 text-xs sm:grid sm:grid-cols-7 hover:bg-slate-100">

                <div class="flex items-center sm:col-span-1">
                    <div class="object-cover w-10 h-10 mr-3 bg-blue-200 rounded-full"></div>
                </div>
                <div class="col-span-3 text-left">
                    <p>はらーだ</p>
                </div>

                <div class="ml-16 text-left sm:mt-0 sm:ml-0 sm:col-span-3">
                    <p class="text-gray-500">
                        @haruemfajfjoafoafof
                    </p>
                </div>
            </button>

            <div class="flex justify-end gap-4 pt-2">
                <button class="px-1 py-2 border border-gray-300 w-28 hover:bg-slate-100"
                    wire:click="closeModal">キャンセル</button>
                <button type="button"
                    class="px-1 py-2 text-red-500 border border-red-500 w-28 hover:bg-red-50">退会</button>
            </div>
        </form>

    </div>
</div>

{{-- <dir class="flex gap-20">
    <div>

        <div x-data>
            <h1 x-text="$wire.count"></h1>

            <button x-on:click="$wire.increment()">Increment</button>
        </div>
    </div>
    <div>

        <div x-data>
            <h1 x-text="$wire.count1"></h1>

            <button x-on:click="$wire.increment1()">Increment</button>
        </div>
    </div>
    <div>

        <div x-data>
            <h1 x-text="$wire.count2"></h1>

            <button x-on:click="$wire.increment2()">Increment</button>
        </div>
    </div>
</dir> --}}