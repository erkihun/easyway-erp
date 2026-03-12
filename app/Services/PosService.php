<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\PosOrderStatus;
use App\Enums\StockMovementType;
use App\Models\PosOrder;
use Illuminate\Support\Facades\DB;

class PosService
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    /** @param array<string,mixed> $payload */
    public function checkout(array $payload): PosOrder
    {
        return DB::transaction(function () use ($payload): PosOrder {
            $order = PosOrder::create([
                'order_number' => $payload['order_number'] ?? 'POS-'.now()->format('YmdHis'),
                'pos_session_id' => $payload['pos_session_id'],
                'customer_id' => $payload['customer_id'] ?? null,
                'status' => PosOrderStatus::Completed,
                'ordered_at' => now(),
            ]);

            $total = 0.0;
            foreach ($payload['items'] as $item) {
                $lineTotal = (float) $item['quantity'] * (float) $item['unit_price'];
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $lineTotal,
                ]);

                $this->inventoryService->decreaseStock(
                    $item['product_id'],
                    $item['warehouse_id'],
                    (float) $item['quantity'],
                    StockMovementType::Sale,
                    'pos_order',
                    $order->id,
                    'POS checkout'
                );

                $total += $lineTotal;
            }

            $order->update(['total_amount' => $total]);

            return $order->load('items');
        });
    }
}
