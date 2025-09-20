<?php

namespace App\Contracts;

interface SaleQueryInterface
{
    public function getSaleDetails(int $saleId): array;
}
