@extends('layouts.admin')
@section('title', __('credit_note.title'))
@section('page-title', __('credit_note.title'))
@section('page-subtitle', __('credit_note.subtitle'))
@section('content')
<x-ui.page-header :title="__('credit_note.title')" :subtitle="__('credit_note.subtitle')" icon="heroicon-o-receipt-percent">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button :href="route('admin.credit-notes.create')">{{ __('credit_note.create_credit_note') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :value="request('q')" :placeholder="__('credit_note.search_placeholder')" />
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q'))
        <x-ui.button variant="ghost" size="sm" :href="route('admin.credit-notes.index')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<x-ui.table-shell :title="__('credit_note.directory')" :count="$creditNotes->total()">
    <x-ui.table compact>
        <thead>
            <tr>
                <th>{{ __('credit_note.credit_note_number') }}</th>
                <th>{{ __('credit_note.customer') }}</th>
                <th>{{ __('credit_note.invoice_reference') }}</th>
                <th>{{ __('credit_note.credit_date') }}</th>
                <th style="text-align:right;">{{ __('credit_note.amount') }}</th>
                <th>{{ __('common.status') }}</th>
                <th class="actions-col">{{ __('common.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($creditNotes as $creditNote)
                <tr>
                    <td>{{ $creditNote->credit_note_number }}</td>
                    <td>{{ $creditNote->customer?->name ?? '-' }}</td>
                    <td>{{ $creditNote->invoice?->invoice_number ?? '-' }}</td>
                    <td>{{ $creditNote->credit_date?->format('Y-m-d') }}</td>
                    <td style="text-align:right;">{{ number_format((float) $creditNote->amount, 2) }}</td>
                    <td><x-ui.status-badge :status="$creditNote->status" /></td>
                    <td class="actions-col">
                        <x-ui.table-actions>
                            <x-ui.button variant="outline" size="sm" :href="route('admin.credit-notes.show', $creditNote)">{{ __('common.view') }}</x-ui.button>
                        </x-ui.table-actions>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">{{ __('common.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </x-ui.table>
    <div class="mt-1">{{ $creditNotes->links() }}</div>
</x-ui.table-shell>
@endsection

