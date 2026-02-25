{{-- Page Header Component --}}
@section('header')
<header class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">
                @yield('page-title', 'Page')
            </h1>
            @hasSection('page-description')
                <p class="mt-1 text-text-secondary">
                    @yield('page-description')
                </p>
            @endif
        </div>
        @hasSection('header-actions')
            <div class="flex items-center gap-3">
                @yield('header-actions')
            </div>
        @endif
    </div>
</header>
@endsection
