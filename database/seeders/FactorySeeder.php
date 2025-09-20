<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;

class FactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏭 Criando dados com factories...');

        // Criar 10 produtos usando factory
        $products = Product::factory(10)->create();
        $this->command->info('✅ 10 produtos criados');

        // Criar estoque para cada produto
        foreach ($products as $product) {
            Inventory::factory()->create([
                'product_id' => $product->id,
                'quantity' => rand(50, 500), // Quantidade aleatória
            ]);
        }
        $this->command->info('✅ Estoque criado para todos os produtos');

        // Criar 5 vendas usando factory
        $sales = Sale::factory(5)->create([
            'status' => 'completed'
        ]);
        $this->command->info('✅ 5 vendas criadas');

        // Criar itens para cada venda
        foreach ($sales as $sale) {
            $numItems = rand(1, 4); // 1 a 4 itens por venda
            $selectedProducts = $products->random($numItems);
            
            $totalAmount = 0;
            $totalCost = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                $unitPrice = $product->sale_price;
                $unitCost = $product->cost_price;

                SaleItem::factory()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                ]);

                $totalAmount += $unitPrice * $quantity;
                $totalCost += $unitCost * $quantity;
            }

            // Atualizar totais da venda
            $sale->update([
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'total_profit' => $totalAmount - $totalCost,
            ]);
        }
        $this->command->info('✅ Itens de venda criados');

        $this->command->info('🎉 Dados criados com sucesso!');
        $this->command->info('📊 Resumo:');
        $this->command->info('   - Produtos: ' . Product::count());
        $this->command->info('   - Itens em estoque: ' . Inventory::sum('quantity'));
        $this->command->info('   - Vendas: ' . Sale::count());
        $this->command->info('   - Itens vendidos: ' . SaleItem::count());
    }
}
