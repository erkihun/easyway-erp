<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasUuidV7;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];
}
