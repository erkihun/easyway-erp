<?php
declare(strict_types=1);

namespace App\Services;

class BarcodeService
{
    public function make(string $seed): string
    {
        $digits = preg_replace('/\D+/', '', $seed) ?: '100000000000';

        return substr(str_pad($digits, 12, '0'), 0, 12);
    }
}
