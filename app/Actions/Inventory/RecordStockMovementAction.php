<?php
declare(strict_types=1);

namespace App\Actions\Inventory;

use App\Enums\StockMovementType;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class RecordStockMovementAction
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(array $attributes): StockMovement
    {
        return StockMovement::create([
            'product_id' => $attributes['product_id'],
            'warehouse_id' => $attributes['warehouse_id'],
            'movement_type' => $attributes['movement_type'] instanceof StockMovementType
                ? $attributes['movement_type']->value
                : $attributes['movement_type'],
            'quantity' => $attributes['quantity'],
            'reference_type' => $attributes['reference_type'] ?? null,
            'reference_id' => $attributes['reference_id'] ?? null,
            'reason' => $attributes['reason'] ?? null,
            'created_by' => $attributes['created_by'] ?? Auth::id(),
        ]);
    }
}
