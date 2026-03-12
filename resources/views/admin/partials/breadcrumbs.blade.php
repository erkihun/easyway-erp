@if(isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs))
<div class="mb-1 muted" style="font-size:.92rem;">
    @foreach($breadcrumbs as $index => $crumb)
        @if($index > 0) / @endif
        @if(isset($crumb['url']))
            <a class="link" href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
        @else
            <span>{{ $crumb['label'] }}</span>
        @endif
    @endforeach
</div>
@endif




