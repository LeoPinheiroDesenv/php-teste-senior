<?php

namespace App\Contracts;

interface SaleRepositoryInterface
{
    public function createSale(array $items): array;
    public function getSaleDetails(int $saleId): array;
    public function processSale(int $saleId): void;
}
