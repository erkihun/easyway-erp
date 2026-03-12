<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductionOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionOrder extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => ProductionOrderStatus::class,
            'planned_quantity' => 'decimal:4',
            'produced_quantity' => 'decimal:4',
            'planned_date' => 'date',
        ];
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
