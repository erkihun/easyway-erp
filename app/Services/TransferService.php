<?php
declare(strict_types=1);

namespace App\Services;

use App\Actions\Transfers\CreateTransferAction;
use App\Models\Transfer;

class TransferService
{
    public function __construct(private readonly CreateTransferAction $createTransferAction)
    {
    }

    /** @param array<string,mixed> $payload */
    public function process(array $payload): Transfer
    {
        return $this->createTransferAction->execute($payload);
    }
}
