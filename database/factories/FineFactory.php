<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Fine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fine>
 */
class FineFactory extends Factory
{
    protected $model = Fine::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'paid']);

        return [
            'loan_id' => Loan::factory(),
            'amount' => fake()->randomFloat(2, 5, 50),
            'reason' => fake()->sentence(),
            'status' => $status,
            'paid_at' => $status === 'paid' ? now() : null,
        ];
    }
}
