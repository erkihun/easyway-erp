@extends('layouts.admin')
@section('title','Accounting')
@section('page-title', __('accounting.title'))
@section('page-subtitle', __('accounting.title'))
@section('content')
<x-ui.page-header title="Accounting Foundation" subtitle="Review recent journal entries and post manual transactions." icon="heroicon-o-calculator">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" :href="route('admin.accounting.accounts.index')" icon="heroicon-o-banknotes">{{ __('accounting.chart_of_accounts') }}</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.accounting.journal-entries.index')" icon="heroicon-o-book-open">{{ __('accounting.journal_entries') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.accounting.accounts.index') }}" class="subnav-tab">{{ __('accounting.chart_of_accounts') }}</a>
    <a href="{{ route('admin.accounting.journal-entries.index') }}" class="subnav-tab">{{ __('accounting.journal_entries') }}</a>
</x-ui.subnav-tabs>

<x-ui.card class="mb-1">
    <x-slot:header>
        <h3 class="table-shell-title">Post Manual Journal Entry</h3>
    </x-slot:header>

    <form method="POST" action="{{ route('admin.accounting.journals.store') }}">
        @csrf
        <div class="row">
            <x-ui.input name="memo" label="Memo" placeholder="Adjustment memo" required />
            <x-ui.input name="amount" label="Amount" type="number" step="0.0001" min="0.01" required />
        </div>
        <div class="form-actions-sticky mt-1">
            <x-ui.button variant="ghost" :href="route('admin.accounting.journal-entries.index')">{{ __('common.cancel') }}</x-ui.button>
            <x-ui.button type="submit" icon="heroicon-o-plus-circle">{{ __('messages.journal_posted') }}</x-ui.button>
        </div>
    </form>
</x-ui.card>

<x-ui.table-shell title="Recent Entries" :count="$entries->count()">
    <table>
        <thead><tr><th>Entry</th><th>{{ __('common.date') }}</th><th>Memo</th><th style="text-align:right;">Lines</th></tr></thead>
        <tbody>
        @forelse($entries as $entry)
            <tr>
                <td><a class="link" href="{{ route('admin.accounting.journal-entries.show',$entry) }}">{{ $entry->entry_number }}</a></td>
                <td>{{ $entry->entry_date }}</td>
                <td>{{ $entry->memo }}</td>
                <td style="text-align:right;">{{ $entry->lines->count() }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">No journal entries posted.</td></tr>
        @endforelse
        </tbody>
    </table>
</x-ui.table-shell>
@endsection





