@props([
    'title' => null,
    'subtitle' => null,
    'maxWidth' => '640px',
    'show' => 'open',
])

<div x-cloak x-show="{{ $show }}" class="modal-overlay" style="display:none;" @keydown.escape.window="{{ $show }} = false">
    <div class="modal-backdrop" @click="{{ $show }} = false"></div>
    <div class="modal-shell" style="max-width: {{ $maxWidth }};">
        <div class="panel modal-panel">
            @if($title || $subtitle)
                <div class="panel-body modal-head">
                    @if($title)
                        <h3 class="modal-title">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="modal-subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif
            <div class="panel-body">
                {{ $slot }}
            </div>
            @if(trim((string) ($footer ?? '')) !== '')
                <div class="panel-body modal-foot">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

