<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasUuidV7;

    protected $keyType = 'string';

    public $incrementing = false;
}
