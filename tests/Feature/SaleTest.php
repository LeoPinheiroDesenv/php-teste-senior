<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_sale(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'sku' => 'TEST-001',
            'name' => 'Produto Teste',
            'cost_price' => 10.00,
            'sale_price' => 20.00,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 100,
            'last_updated' => now(),
        ]);

        $response = $this->postJson('/api/sales', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ]
            ]
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Venda registrada com sucesso',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'sale_id',
                        'status',
                        'total_amount',
                        'total_profit',
                    ]
                ]);

        $this->assertDatabaseHas('sales', [
            'total_amount' => 100.00, // 5 * 20.00
            'total_cost' => 50.00,    // 5 * 10.00
            'total_profit' => 50.00,  // 100.00 - 50.00
            'status' => 'pending',
        ]);

        Queue::assertPushed(\App\Jobs\ProcessSale::class);
    }

    public function test_can_get_sale_details(): void
    {
        $product = Product::factory()->create([
            'sku' => 'TEST-002',
            'name' => 'Produto Teste 2',
            'cost_price' => 15.00,
            'sale_price' => 30.00,
        ]);

        $sale = Sale::create([
            'total_amount' => 60.00,
            'total_cost' => 30.00,
            'total_profit' => 30.00,
            'status' => 'completed',
        ]);

        $sale->saleItems()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 30.00,
            'unit_cost' => 15.00,
        ]);

        $response = $this->getJson("/api/sales/{$sale->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                ])
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'total_amount',
                        'total_cost',
                        'total_profit',
                        'status',
                        'sale_items' => [
                            '*' => [
                                'id',
                                'product_id',
                                'quantity',
                                'unit_price',
                                'unit_cost',
                                'product' => [
                                    'id',
                                    'sku',
                                    'name',
                                    'cost_price',
                                    'sale_price',
                                ]
                            ]
                        ]
                    ]
                ]);
    }

    public function test_sale_validation(): void
    {
        $response = $this->postJson('/api/sales', [
            'items' => []
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Dados inválidos',
                ]);
    }
}
