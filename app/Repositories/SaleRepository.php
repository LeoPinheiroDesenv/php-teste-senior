<?php

namespace App\Repositories;

use App\Contracts\SaleRepositoryInterface;
use App\Contracts\InventoryRepositoryInterface;
use App\Contracts\SaleCreationInterface;
use App\Contracts\SaleQueryInterface;
use App\Contracts\SaleProcessingInterface;
use App\Models\Sale;
use App\Models\Product;
use App\Jobs\ProcessSale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleRepository implements SaleRepositoryInterface, SaleCreationInterface, SaleQueryInterface, SaleProcessingInterface
{
    protected InventoryRepositoryInterface $inventoryRepository;

    public function __construct(InventoryRepositoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Registrar uma nova venda
     */
    public function createSale(array $items): array
    {
        try {
            DB::beginTransaction();

            // Criar a venda
            $sale = Sale::create([
                'total_amount' => 0,
                'total_cost' => 0,
                'total_profit' => 0,
                'status' => 'pending'
            ]);

            // Calcular totais e criar itens
            $totals = $this->calculateTotalsAndCreateItems($sale, $items);

            // Atualizar totais da venda
            $sale->update($totals);

            DB::commit();

            // Processar venda de forma assíncrona
            ProcessSale::dispatch($sale->id);

            Log::info("Venda {$sale->id} criada com sucesso");

            return [
                'success' => true,
                'sale_id' => $sale->id,
                'status' => 'processing',
                'total_amount' => $sale->total_amount,
                'total_profit' => $sale->total_profit
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao criar venda: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calcular totais e criar itens da venda
     */
    protected function calculateTotalsAndCreateItems(Sale $sale, array $items): array
    {
        $totalAmount = 0;
        $totalCost = 0;

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            
            $unitPrice = $product->sale_price;
            $unitCost = $product->cost_price;
            $quantity = $item['quantity'];

            $totalAmount += $unitPrice * $quantity;
            $totalCost += $unitCost * $quantity;

            // Criar item da venda
            $sale->saleItems()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
            ]);
        }

        return [
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'total_profit' => $totalAmount - $totalCost,
        ];
    }

    /**
     * Obter detalhes de uma venda específica
     */
    public function getSaleDetails(int $saleId): array
    {
        try {
            $sale = Sale::with([
                'saleItems.product:id,sku,name,cost_price,sale_price'
            ])->findOrFail($saleId);

            return [
                'success' => true,
                'sale' => $sale
            ];

        } catch (\Exception $e) {
            Log::error("Erro ao buscar venda {$saleId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Processar venda (atualizar estoque)
     */
    public function processSale(int $saleId): void
    {
        try {
            DB::beginTransaction();

            $sale = Sale::with('saleItems')->findOrFail($saleId);

            // Verificar se há estoque suficiente para todos os itens
            foreach ($sale->saleItems as $item) {
                if (!$this->inventoryRepository->hasEnoughStock($item->product_id, $item->quantity)) {
                    $sale->update(['status' => 'cancelled']);
                    DB::rollBack();
                    
                    Log::warning("Venda {$saleId} cancelada: estoque insuficiente para produto {$item->product_id}");
                    return;
                }
            }

            // Atualizar estoque
            foreach ($sale->saleItems as $item) {
                $this->inventoryRepository->reduceStock($item->product_id, $item->quantity);
            }

            // Marcar venda como concluída
            $sale->update(['status' => 'completed']);

            DB::commit();

            Log::info("Venda {$saleId} processada com sucesso");

        } catch (\Exception $e) {
            DB::rollBack();
            
            $sale = Sale::find($saleId);
            if ($sale) {
                $sale->update(['status' => 'cancelled']);
            }

            Log::error("Erro ao processar venda {$saleId}: " . $e->getMessage());
            throw $e;
        }
    }
}
