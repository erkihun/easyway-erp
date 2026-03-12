<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\PosOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrder extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => PosOrderStatus::class,
            'total_amount' => 'decimal:4',
            'ordered_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(PosSession::class, 'pos_session_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosOrderItem::class);
    }
}
