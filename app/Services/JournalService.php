<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public function ensureDefaultAccounts(): void
    {
        $defaults = [
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset'],
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'asset'],
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability'],
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'income'],
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense'],
            ['code' => '1300', 'name' => 'Inventory', 'type' => 'asset'],
        ];

        foreach ($defaults as $account) {
            Account::query()->updateOrCreate(['code' => $account['code']], $account);
        }
    }

    public function postSimpleEntry(string $entryNumber, string $referenceType, string $referenceId, string $memo, float $amount): JournalEntry
    {
        $this->ensureDefaultAccounts();

        return DB::transaction(function () use ($entryNumber, $referenceType, $referenceId, $memo, $amount): JournalEntry {
            $cash = Account::query()->where('code', '1000')->firstOrFail();
            $revenue = Account::query()->where('code', '4000')->firstOrFail();

            $entry = JournalEntry::create([
                'entry_number' => $entryNumber,
                'entry_date' => now()->toDateString(),
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'memo' => $memo,
                'created_by' => auth()->id(),
            ]);

            $entry->lines()->createMany([
                [
                    'account_id' => $cash->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => $memo,
                ],
                [
                    'account_id' => $revenue->id,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => $memo,
                ],
            ]);

            return $entry;
        });
    }
}
