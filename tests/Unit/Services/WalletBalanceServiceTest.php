<?php

namespace Tests\Unit\Services;

use App\Domain\Dashboard\Services\WalletBalanceService;
use App\Models\IncomeTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletBalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private WalletBalanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WalletBalanceService();
    }

    /** @test */
    public function it_calculates_total_balance_with_no_transactions()
    {
        $user = User::factory()->create();
        Wallet::factory()->create([
            'user_id' => $user->id,
            'initial_balance' => 10000, // 100.00 in cents
        ]);

        $balance = $this->service->getTotalBalance($user->id);

        $this->assertEquals(100.00, $balance);
    }

    /** @test */
    public function it_calculates_total_balance_with_multiple_wallets()
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
            'initial_balance' => 10000, // 100.00
        ]);

        Wallet::factory()->create([
            'user_id' => $user->id,
            'initial_balance' => 5000, // 50.00
        ]);

        $balance = $this->service->getTotalBalance($user->id);

        $this->assertEquals(150.00, $balance);
    }

    /** @test */
    public function it_calculates_total_balance_with_received_incomes()
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
            'initial_balance' => 10000, // 100.00
        ]);

        IncomeTransaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 5000, // 50.00 in cents
            'is_received' => true,
        ]);

        $balance = $this->service->getTotalBalance($user->id);

        $this->assertEquals(150.00, $balance); // 100 + 50
    }

    /** @test */
    public function it_calculates_total_balance_with_paid_transactions()
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
            'initial_balance' => 10000, // 100.00
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'amount' => 3000, // 30.00 in cents
            'status' => \App\Enums\TransactionStatusEnum::PAID,
        ]);

        $balance = $this->service->getTotalBalance($user->id);

        $this->assertEquals(70.00, $balance); // 100 - 30
    }

    /** @test */
    public function it_calculates_balance_percentage_change_correctly()
    {
        $user = User::factory()->create();

        $currentBalance = 150.00;
        $thisMonthIncome = 100.00;
        $thisMonthExpenses = 50.00;

        $percentageChange = $this->service->getBalancePercentageChange(
            $user->id,
            $currentBalance,
            $thisMonthIncome,
            $thisMonthExpenses
        );

        // Last month balance = 150 - (100 - 50) = 100
        // Change = ((150 - 100) / 100) * 100 = 50%
        $this->assertEquals(50.0, $percentageChange);
    }

    /** @test */
    public function it_returns_zero_percentage_when_last_balance_is_zero()
    {
        $user = User::factory()->create();

        $percentageChange = $this->service->getBalancePercentageChange(
            $user->id,
            100.00, // current
            100.00, // income
            0.00    // expenses
        );

        $this->assertEquals(0.0, $percentageChange);
    }

    /** @test */
    public function it_gets_wallets_summary()
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'Carteira Principal',
            'type' => \App\Enums\WalletTypeEnum::BANK_ACCOUNT,
            'initial_balance' => 10000,
            'status' => true,
        ]);

        $summary = $this->service->getWalletsSummary($user->id);

        $this->assertCount(1, $summary);
        $this->assertEquals('Carteira Principal', $summary[0]['name']);
        $this->assertEquals(\App\Enums\WalletTypeEnum::BANK_ACCOUNT->value, $summary[0]['type']);
    }

    /** @test */
    public function it_includes_credit_card_usage_in_summary()
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
            'type' => \App\Enums\WalletTypeEnum::CREDIT_CARD,
            'card_limit' => 100000, // 1000.00 in cents
            'card_limit_used' => 30000, // 300.00
            'status' => true,
        ]);

        $summary = $this->service->getWalletsSummary($user->id);

        $this->assertEquals(1000.00, $summary[0]['card_limit']);
        $this->assertEquals(300.00, $summary[0]['card_limit_used']);
        $this->assertEquals(30.0, $summary[0]['usage_percentage']);
    }
}
