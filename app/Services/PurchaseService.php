<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Enums\StockMovementType;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    /** @param array<string,mixed> $payload */
    public function createOrder(array $payload): PurchaseOrder
    {
        return DB::transaction(function () use ($payload): PurchaseOrder {
            $order = PurchaseOrder::create([
                'order_number' => $payload['order_number'] ?? 'PO-'.now()->format('YmdHis'),
                'supplier_id' => $payload['supplier_id'] ?? null,
                'status' => $payload['status'] ?? PurchaseOrderStatus::Approved,
                'order_date' => $payload['order_date'] ?? now()->toDateString(),
                'created_by' => $payload['created_by'] ?? auth()->id(),
            ]);

            $total = 0.0;
            foreach ($payload['items'] as $item) {
                $lineTotal = (float) $item['quantity'] * (float) $item['unit_cost'];
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $lineTotal,
                ]);
                $total += $lineTotal;
            }

            $order->update(['total_amount' => $total]);

            return $order->load('items');
        });
    }

    /** @param array<string,mixed> $payload */
    public function receiveGoods(array $payload): GoodsReceipt
    {
        return DB::transaction(function () use ($payload): GoodsReceipt {
            $receipt = GoodsReceipt::create([
                'receipt_number' => $payload['receipt_number'] ?? 'GRN-'.now()->format('YmdHis'),
                'purchase_order_id' => $payload['purchase_order_id'],
                'warehouse_id' => $payload['warehouse_id'],
                'received_at' => $payload['received_at'] ?? now()->toDateString(),
                'notes' => $payload['notes'] ?? null,
                'created_by' => $payload['created_by'] ?? auth()->id(),
            ]);

            $purchaseOrder = PurchaseOrder::query()->with('items')->findOrFail($payload['purchase_order_id']);
            foreach ($payload['items'] as $item) {
                $poItem = $purchaseOrder->items->firstWhere('id', $item['purchase_order_item_id']);
                if (! $poItem) {
                    continue;
                }

                $quantity = (float) $item['quantity'];
                $poItem->increment('received_quantity', $quantity);

                $this->inventoryService->increaseStock(
                    $poItem->product_id,
                    $payload['warehouse_id'],
                    $quantity,
                    StockMovementType::Purchase,
                    'goods_receipt',
                    $receipt->id,
                    'Goods receipt stock increase'
                );
            }

            return $receipt;
        });
    }
}
