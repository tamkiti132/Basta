<div class="mt-12 text-xs">
    @if ($labels)
    @foreach ($labels as $label)
    <div wire:key="{{ $label->id }}" class="inline-block px-3 py-1 m-1 font-bold text-gray-600 bg-gray-300 rounded-2xl">
        {{ $label->name }}</div>
    @endforeach
    @endif
</div>