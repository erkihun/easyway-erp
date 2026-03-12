<?php
declare(strict_types=1);

namespace App\Enums;

enum TransferStatus: string
{
    case Draft = 'draft';
    case InTransit = 'in_transit';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
