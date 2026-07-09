<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountant = User::where('email', 'accountant@hostel.test')->first();
        $methods = PaymentMethod::cases();

        $twoMonthsAgo = now()->subMonths(2)->startOfMonth()->toDateString();
        $lastMonth = now()->subMonths(1)->startOfMonth()->toDateString();

        // Two months ago: everyone pays in full.
        Invoice::where('billing_month', $twoMonthsAgo)->each(function (Invoice $invoice) use ($accountant, $methods) {
            $invoice->payments()->create([
                'amount' => $invoice->total_amount,
                'payment_method' => fake()->randomElement($methods),
                'paid_at' => $invoice->billing_month->copy()->addDays(5),
                'recorded_by' => $accountant?->id,
            ]);
        });

        // Last month: half pay in full, half pay only part of the balance —
        // the rest stay overdue so "Pending Payments" has something to show.
        Invoice::where('billing_month', $lastMonth)->get()->each(function (Invoice $invoice, int $index) use ($accountant, $methods) {
            if ($index % 2 === 0) {
                $invoice->payments()->create([
                    'amount' => $invoice->total_amount,
                    'payment_method' => fake()->randomElement($methods),
                    'paid_at' => $invoice->due_date->copy()->subDays(2),
                    'recorded_by' => $accountant?->id,
                ]);
            } elseif ($index % 4 === 1) {
                $invoice->payments()->create([
                    'amount' => round($invoice->total_amount * 0.4, 2),
                    'payment_method' => fake()->randomElement($methods),
                    'paid_at' => $invoice->due_date->copy()->subDays(3),
                    'recorded_by' => $accountant?->id,
                ]);
            }
        });
    }
}
