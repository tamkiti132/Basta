<x-guest-layout>
    <div class="pt-4 bg-gray-100">
        <div class="flex flex-col items-center pt-6 min-h-screen sm:pt-0">
            <div>
                <x-authentication-card-logo />
            </div>

            <div class="overflow-hidden p-6 mt-6 w-full bg-white shadow-md sm:max-w-2xl sm:rounded-lg prose">
                {!! $policy !!}
            </div>
        </div>
    </div>
</x-guest-layout>
