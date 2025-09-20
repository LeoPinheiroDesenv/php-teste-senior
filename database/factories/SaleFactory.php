<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalAmount = $this->faker->randomFloat(2, 50, 500);
        $totalCost = $totalAmount * 0.6; // 60% do valor total como custo
        $totalProfit = $totalAmount - $totalCost;

        return [
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}