<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitOfMeasure extends BaseModel
{
    use HasFactory;

    protected $table = 'units_of_measure';

    protected $guarded = [];
}
