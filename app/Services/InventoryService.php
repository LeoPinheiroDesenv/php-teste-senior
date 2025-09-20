<?php

namespace App\Services;

use App\Contracts\InventoryRepositoryInterface;

class InventoryService
{
    protected InventoryRepositoryInterface $inventoryRepository;

    public function __construct(InventoryRepositoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Registrar entrada de produtos no estoque
     */
    public function addStock(int $productId, int $quantity): array
    {
        return $this->inventoryRepository->addStock($productId, $quantity);
    }

    /**
     * Obter situação atual do estoque (otimizada)
     */
    public function getCurrentStock(): array
    {
        return $this->inventoryRepository->getCurrentStock();
    }

    /**
     * Verificar se há estoque suficiente
     */
    public function hasEnoughStock(int $productId, int $quantity): bool
    {
        return $this->inventoryRepository->hasEnoughStock($productId, $quantity);
    }

    /**
     * Reduzir estoque
     */
    public function reduceStock(int $productId, int $quantity): void
    {
        $this->inventoryRepository->reduceStock($productId, $quantity);
    }
}
