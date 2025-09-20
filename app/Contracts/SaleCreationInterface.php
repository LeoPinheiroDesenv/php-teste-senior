<?php

namespace App\Contracts;

interface SaleCreationInterface
{
    public function createSale(array $items): array;
}
