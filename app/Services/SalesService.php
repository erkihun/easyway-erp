<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\SalesOrderStatus;
use App\Enums\StockMovementType;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    /** @param array<string,mixed> $payload */
    public function confirmOrder(array $payload): SalesOrder
    {
        return DB::transaction(function () use ($payload): SalesOrder {
            $order = SalesOrder::create([
                'order_number' => $payload['order_number'] ?? 'SO-'.now()->format('YmdHis'),
                'customer_id' => $payload['customer_id'] ?? null,
                'status' => $payload['status'] ?? SalesOrderStatus::Confirmed,
                'order_date' => $payload['order_date'] ?? now()->toDateString(),
                'created_by' => $payload['created_by'] ?? auth()->id(),
            ]);

            $subtotal = 0.0;
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
                    'sales_order',
                    $order->id,
                    'Sales order deduction'
                );

                $subtotal += $lineTotal;
            }

            $order->update(['subtotal' => $subtotal, 'total_amount' => $subtotal]);

            return $order->load('items');
        });
    }
}
