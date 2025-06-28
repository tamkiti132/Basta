<a href="
    @can('admin-top'){{ route('admin.user_top') }}
    @else {{ route('index') }}
    @endcan">
    <img src="{{ asset('images/logo.png') }}" alt="" class="mx-auto w-28 h-28 sm:w-40 sm:h-40">
</a>
