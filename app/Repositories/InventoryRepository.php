<?php

namespace App\Repositories;

use App\Contracts\InventoryRepositoryInterface;
use App\Contracts\StockOperationsInterface;
use App\Contracts\StockQueryInterface;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryRepository implements InventoryRepositoryInterface, StockOperationsInterface, StockQueryInterface
{
    /**
     * Registrar entrada de produtos no estoque
     */
    public function addStock(int $productId, int $quantity): array
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($productId);
            
            // Verificar se já existe registro de estoque
            $existingInventory = Inventory::where('product_id', $productId)->first();
            
            if ($existingInventory) {
                // Atualizar estoque existente
                $existingInventory->update([
                    'quantity' => $existingInventory->quantity + $quantity,
                    'last_updated' => now(),
                ]);
                $inventory = $existingInventory;
            } else {
                // Criar novo registro de estoque
                $inventory = Inventory::create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'last_updated' => now(),
                ]);
            }

            DB::commit();

            Log::info("Estoque atualizado para produto {$productId}: +{$quantity} unidades");

            return [
                'success' => true,
                'product' => $product,
                'inventory' => $inventory->fresh()
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar estoque: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obter situação atual do estoque (otimizada)
     */
    public function getCurrentStock(): array
    {
        try {
            $inventory = DB::table('inventory as i')
                ->join('products as p', 'i.product_id', '=', 'p.id')
                ->select([
                    'i.id',
                    'i.product_id',
                    'p.sku',
                    'p.name',
                    'p.cost_price',
                    'p.sale_price',
                    'i.quantity',
                    'i.last_updated',
                    DB::raw('(i.quantity * p.cost_price) as total_cost'),
                    DB::raw('(i.quantity * p.sale_price) as total_value'),
                    DB::raw('(i.quantity * (p.sale_price - p.cost_price)) as projected_profit')
                ])
                ->orderBy('p.name')
                ->get();

            $summary = [
                'total_products' => $inventory->count(),
                'total_quantity' => $inventory->sum('quantity'),
                'total_cost' => $inventory->sum('total_cost'),
                'total_value' => $inventory->sum('total_value'),
                'total_projected_profit' => $inventory->sum('projected_profit'),
            ];

            return [
                'success' => true,
                'inventory' => $inventory,
                'summary' => $summary
            ];

        } catch (\Exception $e) {
            Log::error("Erro ao consultar estoque: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verificar se há estoque suficiente
     */
    public function hasEnoughStock(int $productId, int $quantity): bool
    {
        $inventory = Inventory::where('product_id', $productId)->first();
        return $inventory && $inventory->quantity >= $quantity;
    }

    /**
     * Reduzir estoque
     */
    public function reduceStock(int $productId, int $quantity): void
    {
        $inventory = Inventory::where('product_id', $productId)->first();
        
        if (!$inventory || $inventory->quantity < $quantity) {
            throw new \Exception("Estoque insuficiente para produto {$productId}");
        }

        $inventory->update([
            'quantity' => $inventory->quantity - $quantity,
            'last_updated' => now(),
        ]);

        Log::info("Estoque reduzido para produto {$productId}: -{$quantity} unidades");
    }
}
