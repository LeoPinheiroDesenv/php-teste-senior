<?php

namespace App\Services;

use App\Contracts\StockOperationsInterface;
use App\Contracts\StockQueryInterface;

class StockManager implements StockOperationsInterface, StockQueryInterface
{
    protected StockOperationsInterface $stockOperations;
    protected StockQueryInterface $stockQuery;

    public function __construct(StockOperationsInterface $stockOperations, StockQueryInterface $stockQuery)
    {
        $this->stockOperations = $stockOperations;
        $this->stockQuery = $stockQuery;
    }

    // Implementações de StockOperationsInterface
    public function addStock(int $productId, int $quantity): array
    {
        return $this->stockOperations->addStock($productId, $quantity);
    }

    public function reduceStock(int $productId, int $quantity): void
    {
        $this->stockOperations->reduceStock($productId, $quantity);
    }

    public function hasEnoughStock(int $productId, int $quantity): bool
    {
        return $this->stockOperations->hasEnoughStock($productId, $quantity);
    }

    // Implementações de StockQueryInterface
    public function getCurrentStock(): array
    {
        return $this->stockQuery->getCurrentStock();
    }
}
