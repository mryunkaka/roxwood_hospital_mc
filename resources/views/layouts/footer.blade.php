{{-- Footer Component --}}
<footer class="h-14 bg-surface border-t border-border flex items-center justify-between px-4 lg:px-6">
    {{-- Left: Copyright --}}
    <p class="text-sm text-text-secondary">
        &copy; {{ date('Y') }} {{ __('messages.app_name') }}. {{ __('messages.all_rights_reserved') }}
    </p>

    {{-- Right: Version & Links --}}
    <div class="flex items-center gap-4">
        <span class="hidden sm:inline text-xs text-text-tertiary">v1.0.0</span>
        <a href="#" class="text-sm text-text-secondary hover:text-primary transition-colors">
            {{ __('messages.help') }}
        </a>
    </div>
</footer>
