@props(['title', 'description', 'action' => null, 'icon' => 'folder-off'])

<div class="empty">
    <div class="empty-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            @if($icon == 'folder-off')
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M3 3l18 18" />
                <path d="M19 19a2 2 0 0 1 -2 2h-13a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v4" />
            @else
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <!-- Generic fallback or use specific icon based on prop if needed custom svg -->
                <circle cx="12" cy="12" r="9" />
                <line x1="9" y1="10" x2="9.01" y2="10" />
                <line x1="15" y1="10" x2="15.01" y2="10" />
                <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" />
            @endif
        </svg>
    </div>
    <p class="empty-title">{{ $title }}</p>
    <p class="empty-subtitle text-muted">
        {{ $description }}
    </p>
    @if($action)
        <div class="empty-action">
            {!! $action !!}
        </div>
    @endif
</div>