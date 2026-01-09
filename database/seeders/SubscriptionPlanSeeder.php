<?php

namespace Database\Seeders;

use App\Enums\PlanTypeEnum;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Gratuito',
                'slug' => PlanTypeEnum::FREE->value,
                'price_cents' => 0,
                'billing_period' => 'monthly',
                'description' => 'Plano gratuito com recursos básicos para começar',
                'max_users' => 1,
                'features' => [
                    'categories' => 10,
                    'wallets' => 3,
                    'budgets' => 5,
                    'savings_goals' => 0,
                    'export_per_month' => 5,
                    'transactions_history_months' => 12,
                    'tags' => 0,
                    'attachments' => 0,
                    'custom_reports' => 0,
                    'ai_predictions' => false,
                ],
            ],
            [
                'name' => 'Premium',
                'slug' => PlanTypeEnum::PREMIUM->value,
                'price_cents' => 1990, // R$ 19,90
                'billing_period' => 'monthly',
                'description' => 'Plano premium com recursos avançados e sem limites',
                'max_users' => 1,
                'features' => [
                    'categories' => -1, // Ilimitado
                    'wallets' => -1,
                    'budgets' => -1,
                    'savings_goals' => 20,
                    'export_per_month' => -1,
                    'transactions_history_months' => -1,
                    'tags' => -1,
                    'attachments' => 100,
                    'custom_reports' => 50,
                    'ai_predictions' => true,
                ],
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $planData['name'],
                    'price_cents' => $planData['price_cents'],
                    'billing_period' => $planData['billing_period'],
                    'description' => $planData['description'],
                    'max_users' => $planData['max_users'],
                    'features' => $planData['features'],
                    'is_active' => true,
                ]
            );
        }
    }
}
