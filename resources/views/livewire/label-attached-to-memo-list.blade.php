<div class="mt-12 text-xs sm:text-sm">
    @foreach ($labels as $label)
    <div class="inline-block px-3 py-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
        {{ $label->name }}</div>
    @endforeach
</div>