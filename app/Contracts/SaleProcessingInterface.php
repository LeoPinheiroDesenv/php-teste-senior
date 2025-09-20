<?php

namespace App\Contracts;

interface SaleProcessingInterface
{
    public function processSale(int $saleId): void;
}
