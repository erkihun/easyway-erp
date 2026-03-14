<div {{ $attributes->merge(['class' => 'panel glass-card']) }}>
    @if(trim((string)($header ?? '')) !== '')
        <div class="panel-body panel-head">
            {{ $header }}
        </div>
    @endif
    <div class="panel-body">
        {{ $slot }}
    </div>
    @if(trim((string)($footer ?? '')) !== '')
        <div class="panel-body panel-foot">
            {{ $footer }}
        </div>
    @endif
</div>


