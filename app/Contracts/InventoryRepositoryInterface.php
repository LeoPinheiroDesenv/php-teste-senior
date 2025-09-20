<?php

namespace App\Contracts;

interface InventoryRepositoryInterface
{
    public function addStock(int $productId, int $quantity): array;
    public function getCurrentStock(): array;
    public function hasEnoughStock(int $productId, int $quantity): bool;
    public function reduceStock(int $productId, int $quantity): void;
}
