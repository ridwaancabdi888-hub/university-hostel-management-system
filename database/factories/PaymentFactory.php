<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Deliberately does not set `receipt_number` — PaymentObserver generates
     * it on creation, so factories exercise the same code path production
     * traffic does.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'recorded_by' => null,
            'amount' => fake()->randomFloat(2, 50, 500),
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'reference_number' => null,
            'paid_at' => now(),
        ];
    }
}
