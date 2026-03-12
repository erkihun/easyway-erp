<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }
}
