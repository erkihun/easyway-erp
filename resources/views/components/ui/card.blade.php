<div {{ $attributes->merge(['class' => 'panel']) }}>
    @if(trim((string)($header ?? '')) !== '')
        <div class="panel-body" style="border-bottom:1px solid var(--line);padding-bottom:.55rem;">
            {{ $header }}
        </div>
    @endif
    <div class="panel-body">
        {{ $slot }}
    </div>
    @if(trim((string)($footer ?? '')) !== '')
        <div class="panel-body" style="border-top:1px solid var(--line);padding-top:.55rem;">
            {{ $footer }}
        </div>
    @endif
</div>


