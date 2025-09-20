<?php

namespace App\Contracts;

interface StockOperationsInterface
{
    public function addStock(int $productId, int $quantity): array;
    public function reduceStock(int $productId, int $quantity): void;
    public function hasEnoughStock(int $productId, int $quantity): bool;
}
