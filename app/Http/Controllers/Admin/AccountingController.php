<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingController extends Controller
{
    public function __construct(private readonly JournalService $journalService)
    {
    }

    public function index(): View
    {
        $this->journalService->ensureDefaultAccounts();

        return view('admin.accounting.index', [
            'accounts' => Account::query()->orderBy('code')->get(),
            'entries' => JournalEntry::query()->with('lines')->latest()->limit(20)->get(),
        ]);
    }

    public function accountsIndex(): View
    {
        $this->journalService->ensureDefaultAccounts();

        $accounts = Account::query()->orderBy('code')->paginate(30);

        return view('admin.accounting.accounts.index', compact('accounts'));
    }

    public function journalEntriesIndex(): View
    {
        $entries = JournalEntry::query()->with('lines')->latest()->paginate(30);

        return view('admin.accounting.journal-entries.index', compact('entries'));
    }

    public function journalEntryShow(JournalEntry $journalEntry): View
    {
        $journalEntry->load(['lines.account']);

        return view('admin.accounting.journal-entries.show', ['entry' => $journalEntry]);
    }

    public function postManualEntry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'memo' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $this->journalService->postSimpleEntry(
            'JE-'.now()->format('YmdHisv'),
            'manual',
            (string) auth()->id(),
            $data['memo'],
            (float) $data['amount']
        );

        return redirect()->route('admin.accounting.journal-entries.index')->with('status', __('messages.journal_posted'));
    }
}


