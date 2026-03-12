@extends('layouts.admin')
@section('title','Chart of Accounts')
@section('page-title', __('accounting.chart_of_accounts'))
@section('page-subtitle', __('accounting.chart_of_accounts'))
@section('content')
<x-ui.page-header title="Accounts" subtitle="Foundation accounts used for journal postings." icon="heroicon-o-calculator">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.accounting.index')">{{ __('navigation.accounting') }}</x-ui.button>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.accounting.journal-entries.index')">{{ __('accounting.journal_entries') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.accounting.accounts.index') }}" class="subnav-tab is-active">{{ __('accounting.chart_of_accounts') }}</a>
    <a href="{{ route('admin.accounting.journal-entries.index') }}" class="subnav-tab">{{ __('accounting.journal_entries') }}</a>
</x-ui.subnav-tabs>

<div class="panel">
    <div class="panel-body">
        @if($accounts->isEmpty())
            <x-ui.empty-state title="No accounts available" description="Default accounts are provisioned by the accounting service." icon="heroicon-o-bookmark-square" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Code</th><th>{{ __('common.name') }}</th><th>Type</th></tr></thead>
                    <tbody>
                    @foreach($accounts as $account)
                        <tr>
                            <td>{{ $account->code }}</td>
                            <td>{{ $account->name }}</td>
                            <td><x-ui.status-badge :status="$account->type" /></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $accounts->links() }}</div>
        @endif
    </div>
</div>
@endsection






