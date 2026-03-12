@extends('layouts.admin')
@section('title','Journal Entries')
@section('page-title', __('accounting.journal_entries'))
@section('page-subtitle', __('accounting.journal_entries'))
@section('content')
<x-ui.page-header title="Journal Entries" subtitle="Review accounting postings and navigate to line-level detail." icon="heroicon-o-book-open">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.accounting.index')">{{ __('navigation.accounting') }}</x-ui.button>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.accounting.accounts.index')">{{ __('accounting.chart_of_accounts') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.accounting.accounts.index') }}" class="subnav-tab">{{ __('accounting.chart_of_accounts') }}</a>
    <a href="{{ route('admin.accounting.journal-entries.index') }}" class="subnav-tab is-active">{{ __('accounting.journal_entries') }}</a>
</x-ui.subnav-tabs>

<div class="panel">
    <div class="panel-body">
        @if($entries->isEmpty())
            <x-ui.empty-state title="No journal entries" description="Posted entries will appear here." icon="heroicon-o-book-open" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Entry</th><th>{{ __('common.date') }}</th><th>Reference</th><th>Memo</th><th class="actions-col">{{ __('common.actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            <td>{{ $entry->entry_number }}</td>
                            <td>{{ $entry->entry_date }}</td>
                            <td>{{ $entry->reference_type }} {{ $entry->reference_id }}</td>
                            <td>{{ $entry->memo }}</td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.accounting.journal-entries.show',$entry)">{{ __('common.show') }}</x-ui.button>
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $entries->links() }}</div>
        @endif
    </div>
</div>
@endsection






