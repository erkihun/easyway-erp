<?php
declare(strict_types=1);

namespace App\Support;

use App\Models\ActivityLog;

class ActivityLogger
{
    /**
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     */
    public static function log(string $action, string $model, ?string $modelId = null, ?array $oldValues = null, ?array $newValues = null): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'timestamp' => now(),
        ]);
    }
}
