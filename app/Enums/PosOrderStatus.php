<?php
declare(strict_types=1);

namespace App\Enums;

enum PosOrderStatus: string
{
    case Draft = 'draft';
    case Completed = 'completed';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';
}
