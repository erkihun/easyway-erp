@extends('layouts.admin')
@section('title','POS Sessions')
@section('page-title', __('pos.sessions'))
@section('content')
<x-ui.page-header title="POS Sessions" subtitle="Open and close retail operator sessions." icon="heroicon-o-shopping-cart" />

<x-ui.card class="mb-1">
    <form method="POST" action="{{ route('admin.pos.sessions.open') }}">
        @csrf
        <div class="row">
            <x-ui.select name="warehouse_id" label="Warehouse" required>
                @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
            </x-ui.select>
            <x-ui.input name="opening_amount" label="Opening Amount" type="number" step="0.0001" value="0" />
        </div>
        <div class="form-actions-sticky mt-1">
            <x-ui.button variant="ghost" :href="route('admin.pos.index')">{{ __('common.cancel') }}</x-ui.button>
            <x-ui.button type="submit" variant="success" icon="heroicon-o-lock-open">{{ __('pos.sessions') }}</x-ui.button>
        </div>
    </form>
</x-ui.card>

<x-ui.table-shell title="Session History" :count="$sessions->total()">
    <table>
        <thead><tr><th>{{ __('dashboard.warehouse') }}</th><th>{{ __('common.status') }}</th><th>Opened</th><th style="text-align:right;">Closing Amount</th><th class="actions-col">Action</th></tr></thead>
        <tbody>
        @forelse($sessions as $s)
            <tr>
                <td>{{ $s->warehouse?->name }}</td>
                <td><x-ui.status-badge :status="$s->status" /></td>
                <td>{{ $s->opened_at }}</td>
                <td style="text-align:right;">{{ number_format((float)$s->closing_amount,2) }}</td>
                <td class="actions-col">
                    @if(($s->status->value ?? $s->status)==='open')
                        <x-ui.table-actions>
                            <form method="POST" action="{{ route('admin.pos.sessions.close',$s) }}" onsubmit="return confirm('Close this session?')">
                                @csrf
                                <x-ui.button variant="warning" size="sm" type="submit">{{ __('common.close') }}</x-ui.button>
                            </form>
                        </x-ui.table-actions>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">No sessions.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-1">{{ $sessions->links() }}</div>
</x-ui.table-shell>
@endsection






