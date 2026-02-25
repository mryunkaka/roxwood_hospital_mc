{{-- Content Wrapper Component --}}
@section('content-wrapper')
<div class="content-wrapper">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-success-50 border border-success-200 flex items-center gap-3 theme-dark:bg-success-900/20 theme-dark:border-success-800">
            <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-success-700 theme-dark:text-success-300">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-danger-50 border border-danger-200 flex items-center gap-3 theme-dark:bg-danger-900/20 theme-dark:border-danger-800">
            <svg class="w-5 h-5 text-danger-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-danger-700 theme-dark:text-danger-300">{{ session('error') }}</p>
        </div>
    @endif

    @yield('content')
</div>
@endsection
