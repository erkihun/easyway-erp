<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\ProductionOrderStatus;
use App\Enums\StockMovementType;
use App\Models\Bom;
use App\Models\ProductionOrder;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    /** @param array<string,mixed> $payload */
    public function createOrder(array $payload): ProductionOrder
    {
        return ProductionOrder::create([
            'order_number' => $payload['order_number'] ?? 'MO-'.now()->format('YmdHis'),
            'bom_id' => $payload['bom_id'],
            'product_id' => $payload['product_id'],
            'warehouse_id' => $payload['warehouse_id'],
            'status' => ProductionOrderStatus::Released,
            'planned_quantity' => $payload['planned_quantity'],
            'planned_date' => $payload['planned_date'] ?? now()->toDateString(),
        ]);
    }

    public function completeOrder(ProductionOrder $order): ProductionOrder
    {
        return DB::transaction(function () use ($order): ProductionOrder {
            $order->loadMissing('bom.items');
            $bom = Bom::query()->with('items')->findOrFail($order->bom_id);

            foreach ($bom->items as $item) {
                $consumeQty = (float) $item->quantity * (float) $order->planned_quantity;
                $this->inventoryService->decreaseStock(
                    $item->component_product_id,
                    $order->warehouse_id,
                    $consumeQty,
                    StockMovementType::Production,
                    'production_order',
                    $order->id,
                    'Component consumption'
                );
            }

            $this->inventoryService->increaseStock(
                $order->product_id,
                $order->warehouse_id,
                (float) $order->planned_quantity,
                StockMovementType::Production,
                'production_order',
                $order->id,
                'Finished goods output'
            );

            $order->update([
                'status' => ProductionOrderStatus::Completed,
                'produced_quantity' => $order->planned_quantity,
            ]);

            return $order->refresh();
        });
    }
}
