<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_inventory_entry(): void
    {
        $product = Product::factory()->create([
            'sku' => 'TEST-001',
            'name' => 'Produto Teste',
            'cost_price' => 10.00,
            'sale_price' => 20.00,
        ]);

        $response = $this->postJson('/api/inventory', [
            'product_id' => $product->id,
            'quantity' => 100,
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Estoque atualizado com sucesso',
                ]);

        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'quantity' => 100,
        ]);
    }

    public function test_can_get_inventory_status(): void
    {
        $product = Product::factory()->create([
            'sku' => 'TEST-002',
            'name' => 'Produto Teste 2',
            'cost_price' => 15.00,
            'sale_price' => 30.00,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 50,
            'last_updated' => now(),
        ]);

        $response = $this->getJson('/api/inventory');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                ])
                ->assertJsonStructure([
                    'data' => [
                        'inventory' => [
                            '*' => [
                                'id',
                                'product_id',
                                'sku',
                                'name',
                                'cost_price',
                                'sale_price',
                                'quantity',
                                'total_cost',
                                'total_value',
                                'projected_profit',
                            ]
                        ],
                        'summary' => [
                            'total_products',
                            'total_quantity',
                            'total_cost',
                            'total_value',
                            'total_projected_profit',
                        ]
                    ]
                ]);
    }

    public function test_inventory_entry_validation(): void
    {
        $response = $this->postJson('/api/inventory', [
            'product_id' => 999,
            'quantity' => -1,
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Dados inválidos',
                ]);
    }
}
