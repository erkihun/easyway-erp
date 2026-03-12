<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

class SkuGeneratorService
{
    public function generate(string $name): string
    {
        $prefix = Str::upper(Str::substr(Str::slug($name, ''), 0, 6));

        return sprintf('%s-%s', $prefix ?: 'PRD', Str::upper(Str::random(6)));
    }
}
