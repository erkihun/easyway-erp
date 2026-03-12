<?php
declare(strict_types=1);

namespace App\Services;

use App\Actions\Inventory\RecordStockMovementAction;
use App\Enums\StockMovementType;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(private readonly RecordStockMovementAction $recordStockMovementAction)
    {
    }

    public function increaseStock(string $productId, string $warehouseId, float $quantity, StockMovementType $movementType, ?string $referenceType = null, ?string $referenceId = null, ?string $reason = null): void
    {
        $this->recordMovement($productId, $warehouseId, abs($quantity), $movementType, $referenceType, $referenceId, $reason);
    }

    public function decreaseStock(string $productId, string $warehouseId, float $quantity, StockMovementType $movementType, ?string $referenceType = null, ?string $referenceId = null, ?string $reason = null): void
    {
        $available = $this->calculateStock($productId, $warehouseId);
        $requested = abs($quantity);

        if ($requested > $available) {
            throw new \RuntimeException('Insufficient stock available for movement.');
        }

        $this->recordMovement($productId, $warehouseId, -$requested, $movementType, $referenceType, $referenceId, $reason);
    }

    public function transferStock(string $productId, string $sourceWarehouseId, string $destinationWarehouseId, float $quantity, ?string $referenceType = null, ?string $referenceId = null): void
    {
        DB::transaction(function () use ($productId, $sourceWarehouseId, $destinationWarehouseId, $quantity, $referenceType, $referenceId): void {
            $this->decreaseStock($productId, $sourceWarehouseId, $quantity, StockMovementType::TransferOut, $referenceType, $referenceId, 'Warehouse transfer out');
            $this->increaseStock($productId, $destinationWarehouseId, $quantity, StockMovementType::TransferIn, $referenceType, $referenceId, 'Warehouse transfer in');
        });
    }

    public function adjustStock(string $productId, string $warehouseId, float $quantityDelta, ?string $reason = null, ?string $referenceId = null): void
    {
        $movementType = $quantityDelta >= 0 ? StockMovementType::Adjustment : StockMovementType::Damage;
        $this->recordMovement($productId, $warehouseId, $quantityDelta, $movementType, 'stock_adjustment', $referenceId, $reason);
    }

    public function calculateStock(string $productId, string $warehouseId): float
    {
        return (float) DB::table('stock_movements')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->sum('quantity');
    }

    public function reserveStock(string $productId, string $warehouseId, float $quantity): ProductStock
    {
        $stock = ProductStock::firstOrCreate(
            ['product_id' => $productId, 'warehouse_id' => $warehouseId],
            ['cached_quantity' => 0, 'reserved_quantity' => 0]
        );

        $available = $this->calculateStock($productId, $warehouseId) - (float) $stock->reserved_quantity;
        if ($quantity > $available) {
            throw new \RuntimeException('Unable to reserve stock: insufficient available quantity.');
        }

        $stock->reserved_quantity = (float) $stock->reserved_quantity + $quantity;
        $stock->cached_quantity = $this->calculateStock($productId, $warehouseId);
        $stock->last_calculated_at = now();
        $stock->save();

        return $stock;
    }

    private function recordMovement(string $productId, string $warehouseId, float $quantity, StockMovementType $movementType, ?string $referenceType, ?string $referenceId, ?string $reason): void
    {
        DB::transaction(function () use ($productId, $warehouseId, $quantity, $movementType, $referenceType, $referenceId, $reason): void {
            $this->recordStockMovementAction->execute([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reason' => $reason,
            ]);

            $stock = ProductStock::firstOrCreate(
                ['product_id' => $productId, 'warehouse_id' => $warehouseId],
                ['cached_quantity' => 0, 'reserved_quantity' => 0]
            );

            $stock->cached_quantity = $this->calculateStock($productId, $warehouseId);
            $stock->last_calculated_at = now();
            $stock->save();
        });
    }
}
