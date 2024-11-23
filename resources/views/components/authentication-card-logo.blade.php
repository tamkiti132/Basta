<a href="
    @can('admin-top'){{ route('admin.user_top') }}
    @else {{ route('index') }}
    @endcan">
    <img src="{{ asset('images/logo.png') }}" alt="">
</a>
