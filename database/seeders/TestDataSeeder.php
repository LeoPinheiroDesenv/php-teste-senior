<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Inventory;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'sku' => 'PROD-001',
                'name' => 'Smartphone Samsung Galaxy S24',
                'description' => 'Smartphone premium com tela de 6.2 polegadas',
                'cost_price' => 800.00,
                'sale_price' => 1200.00,
            ],
            [
                'sku' => 'PROD-002',
                'name' => 'Notebook Dell Inspiron 15',
                'description' => 'Notebook para uso profissional e pessoal',
                'cost_price' => 1200.00,
                'sale_price' => 1800.00,
            ],
            [
                'sku' => 'PROD-003',
                'name' => 'Fone de Ouvido Bluetooth Sony',
                'description' => 'Fone sem fio com cancelamento de ruído',
                'cost_price' => 150.00,
                'sale_price' => 250.00,
            ],
            [
                'sku' => 'PROD-004',
                'name' => 'Tablet iPad Air',
                'description' => 'Tablet Apple com tela de 10.9 polegadas',
                'cost_price' => 600.00,
                'sale_price' => 900.00,
            ],
            [
                'sku' => 'PROD-005',
                'name' => 'Smartwatch Apple Watch',
                'description' => 'Relógio inteligente com GPS e monitoramento de saúde',
                'cost_price' => 300.00,
                'sale_price' => 450.00,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            // Criar estoque inicial para cada produto
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => rand(50, 200), // Quantidade aleatória entre 50 e 200
                'last_updated' => now(),
            ]);
        }

        $this->command->info('Produtos e estoque inicial criados com sucesso!');
        $this->command->info('Total de produtos: ' . Product::count());
        $this->command->info('Total de itens em estoque: ' . Inventory::sum('quantity'));
    }
}
