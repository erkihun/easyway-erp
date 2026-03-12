<?php
declare(strict_types=1);

namespace App\Enums;

enum StockMovementType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';
    case Adjustment = 'adjustment';
    case Return = 'return';
    case Damage = 'damage';
    case Production = 'production';
}
