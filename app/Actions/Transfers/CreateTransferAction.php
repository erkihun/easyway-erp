<?php
declare(strict_types=1);

namespace App\Actions\Transfers;

use App\Enums\TransferStatus;
use App\Models\Transfer;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class CreateTransferAction
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(array $payload): Transfer
    {
        return DB::transaction(function () use ($payload): Transfer {
            /** @var Transfer $transfer */
            $transfer = Transfer::create([
                'transfer_number' => $payload['transfer_number'] ?? 'TRF-'.now()->format('YmdHis'),
                'source_warehouse_id' => $payload['source_warehouse_id'],
                'destination_warehouse_id' => $payload['destination_warehouse_id'],
                'status' => TransferStatus::Completed,
                'transfer_date' => $payload['transfer_date'] ?? now()->toDateString(),
                'created_by' => $payload['created_by'] ?? auth()->id(),
            ]);

            foreach ($payload['items'] as $item) {
                $transfer->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                $this->inventoryService->transferStock(
                    $item['product_id'],
                    $transfer->source_warehouse_id,
                    $transfer->destination_warehouse_id,
                    (float) $item['quantity'],
                    'transfer',
                    $transfer->id
                );
            }

            return $transfer->load('items');
        });
    }
}
