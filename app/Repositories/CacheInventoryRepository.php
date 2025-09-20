<?php

namespace App\Repositories;

use App\Contracts\InventoryRepositoryInterface;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CacheInventoryRepository implements InventoryRepositoryInterface
{
    protected InventoryRepositoryInterface $inventoryRepository;
    protected int $cacheTtl;

    public function __construct(InventoryRepositoryInterface $inventoryRepository, int $cacheTtl = 3600)
    {
        $this->inventoryRepository = $inventoryRepository;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * Registrar entrada de produtos no estoque
     */
    public function addStock(int $productId, int $quantity): array
    {
        $result = $this->inventoryRepository->addStock($productId, $quantity);
        
        // Invalidar cache relacionado
        $this->invalidateCache();
        
        return $result;
    }

    /**
     * Obter situação atual do estoque (com cache)
     */
    public function getCurrentStock(): array
    {
        return Cache::remember('inventory.current_stock', $this->cacheTtl, function () {
            return $this->inventoryRepository->getCurrentStock();
        });
    }

    /**
     * Verificar se há estoque suficiente (com cache)
     */
    public function hasEnoughStock(int $productId, int $quantity): bool
    {
        $cacheKey = "inventory.stock_check.{$productId}";
        
        return Cache::remember($cacheKey, 300, function () use ($productId, $quantity) {
            return $this->inventoryRepository->hasEnoughStock($productId, $quantity);
        });
    }

    /**
     * Reduzir estoque
     */
    public function reduceStock(int $productId, int $quantity): void
    {
        $this->inventoryRepository->reduceStock($productId, $quantity);
        
        // Invalidar cache relacionado
        $this->invalidateCache();
    }

    /**
     * Invalidar cache relacionado ao estoque
     */
    protected function invalidateCache(): void
    {
        Cache::forget('inventory.current_stock');
        Cache::flush(); // Em produção, seria mais específico
    }
}
