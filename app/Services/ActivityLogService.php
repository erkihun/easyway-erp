<?php
declare(strict_types=1);

namespace App\Services;

use App\Support\ActivityLogger;

class ActivityLogService
{
    /** @param array<string,mixed>|null $old */
    /** @param array<string,mixed>|null $new */
    public function log(string $action, string $model, ?string $modelId = null, ?array $old = null, ?array $new = null): void
    {
        ActivityLogger::log($action, $model, $modelId, $old, $new);
    }
}
