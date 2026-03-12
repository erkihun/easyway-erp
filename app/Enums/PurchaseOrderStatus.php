<?php
declare(strict_types=1);

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Partial = 'partial';
    case Received = 'received';
    case Cancelled = 'cancelled';
}
