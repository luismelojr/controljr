<?php

namespace Tests\Unit\Services;

use App\Domain\Dashboard\Services\FinancialAnalysisService;
use App\Enums\TransactionStatusEnum;
use App\Models\IncomeTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    private FinancialAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FinancialAnalysisService();
    }

    /** @test */
    public function it_calculates_monthly_expenses_correctly()
    {
        $user = User::factory()->create();

        // Create paid transaction for current month
        Transaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 5000, // 50.00 in cents
            'status' => TransactionStatusEnum::PAID,
            'paid_at' => now(),
        ]);

        // Create another paid transaction for current month
        Transaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 3000, // 30.00
            'status' => TransactionStatusEnum::PAID,
            'paid_at' => now(),
        ]);

        // Create pending transaction (should not be counted)
        Transaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 2000,
            'status' => TransactionStatusEnum::PENDING,
            'paid_at' => null,
        ]);

        $expenses = $this->service->getMonthlyExpenses($user->id);

        $this->assertEquals(80.00, $expenses); // 50 + 30
    }

    /** @test */
    public function it_calculates_monthly_income_correctly()
    {
        $user = User::factory()->create();

        // Create received income for current month
        IncomeTransaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 150000, // 1500.00 in cents
            'is_received' => true,
            'received_at' => now(),
        ]);

        // Create another received income for current month
        IncomeTransaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 50000, // 500.00
            'is_received' => true,
            'received_at' => now(),
        ]);

        // Create not received income (should not be counted)
        IncomeTransaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 30000,
            'is_received' => false,
        ]);

        $income = $this->service->getMonthlyIncome($user->id);

        $this->assertEquals(2000.00, $income); // 1500 + 500
    }

    /** @test */
    public function it_calculates_expenses_percentage_change()
    {
        $user = User::factory()->create();

        // Current month: 100.00
        Transaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 10000,
            'status' => TransactionStatusEnum::PAID,
            'paid_at' => now(),
        ]);

        // Last month: 50.00
        Transaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 5000,
            'status' => TransactionStatusEnum::PAID,
            'paid_at' => now()->subMonth(),
        ]);

        $currentMonthExpenses = $this->service->getMonthlyExpenses($user->id);
        $percentageChange = $this->service->getExpensesPercentageChange($user->id, $currentMonthExpenses);

        // (100 - 50) / 50 * 100 = 100%
        $this->assertEquals(100.0, $percentageChange);
    }

    /** @test */
    public function it_returns_zero_percentage_when_last_month_has_no_expenses()
    {
        $user = User::factory()->create();

        Transaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 10000,
            'status' => TransactionStatusEnum::PAID,
            'paid_at' => now(),
        ]);

        $currentMonthExpenses = $this->service->getMonthlyExpenses($user->id);
        $percentageChange = $this->service->getExpensesPercentageChange($user->id, $currentMonthExpenses);

        $this->assertEquals(0.0, $percentageChange);
    }

    /** @test */
    public function it_calculates_income_percentage_change()
    {
        $user = User::factory()->create();

        // Current month: 200.00
        IncomeTransaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 20000,
            'is_received' => true,
            'received_at' => now(),
        ]);

        // Last month: 100.00
        IncomeTransaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 10000,
            'is_received' => true,
            'received_at' => now()->subMonth(),
        ]);

        $currentMonthIncome = $this->service->getMonthlyIncome($user->id);
        $percentageChange = $this->service->getIncomePercentageChange($user->id, $currentMonthIncome);

        // (200 - 100) / 100 * 100 = 100%
        $this->assertEquals(100.0, $percentageChange);
    }

    /** @test */
    public function it_returns_cashflow_data_for_last_6_months()
    {
        $user = User::factory()->create();

        // Create transaction for each of the last 6 months
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);

            Transaction::factory()->create([
                'user_id' => $user->id,
                'amount' => ($i + 1) * 1000, // Different amounts
                'status' => TransactionStatusEnum::PAID,
                'paid_at' => $date,
            ]);

            IncomeTransaction::factory()->create([
                'user_id' => $user->id,
                'amount' => ($i + 1) * 2000, // Double of expenses
                'is_received' => true,
                'received_at' => $date,
            ]);
        }

        $cashflowData = $this->service->getCashflowData($user->id);

        $this->assertArrayHasKey('months', $cashflowData);
        $this->assertArrayHasKey('expenses', $cashflowData);
        $this->assertArrayHasKey('incomes', $cashflowData);

        $this->assertCount(6, $cashflowData['months']);
        $this->assertCount(6, $cashflowData['expenses']);
        $this->assertCount(6, $cashflowData['incomes']);
    }

    /** @test */
    public function it_returns_correct_month_names_in_portuguese()
    {
        $user = User::factory()->create();

        $cashflowData = $this->service->getCashflowData($user->id);

        // Verify we get Portuguese month abbreviations
        foreach ($cashflowData['months'] as $month) {
            $this->assertTrue(in_array($month, [
                'Jan',
                'Fev',
                'Mar',
                'Abr',
                'Mai',
                'Jun',
                'Jul',
                'Ago',
                'Set',
                'Out',
                'Nov',
                'Dez',
            ]));
        }
    }
}
