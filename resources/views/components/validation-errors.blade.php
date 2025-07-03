@if ($errors->any())
<div {{ $attributes }}>
    <ul class="mt-3 ml-3 text-sm list-disc list-inside text-red-600">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif