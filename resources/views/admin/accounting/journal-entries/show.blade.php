@extends('layouts.admin')
@section('title','Journal Entry')
@section('page-title', __('accounting.journal_entries'))
@section('content')
<x-ui.page-header title="Journal Entry {{ $entry->entry_number }}" subtitle="Accounting lines and references." icon="heroicon-o-book-open">
    <x-slot:actions>
        <x-ui.button variant="outline" :href="route('admin.accounting.journal-entries.index')">{{ __('common.back') }}</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
    <x-ui.smart-button label="Entry" :value="$entry->entry_number" icon="heroicon-o-hashtag" />
    <x-ui.smart-button label="Date" :value="(string)$entry->entry_date" icon="heroicon-o-calendar" />
    <x-ui.smart-button label="Reference" :value="trim(($entry->reference_type ?? '').' '.($entry->reference_id ?? ''))" icon="heroicon-o-link" />
</div>

<x-ui.table-shell title="Journal Lines" :count="$entry->lines->count()">
    <table>
        <thead><tr><th>Account</th><th style="text-align:right;">Debit</th><th style="text-align:right;">Credit</th><th>Description</th></tr></thead>
        <tbody>
        @forelse($entry->lines as $line)
            <tr>
                <td>{{ $line->account?->code }} - {{ $line->account?->name }}</td>
                <td style="text-align:right;">{{ number_format((float)$line->debit,2) }}</td>
                <td style="text-align:right;">{{ number_format((float)$line->credit,2) }}</td>
                <td>{{ $line->description }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">No lines.</td></tr>
        @endforelse
        </tbody>
    </table>
</x-ui.table-shell>
@endsection




