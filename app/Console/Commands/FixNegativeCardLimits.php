<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Enums\WalletTypeEnum;
use Illuminate\Console\Command;

class FixNegativeCardLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallets:fix-negative-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix wallets with negative card_limit_used values';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Searching for wallets with negative card_limit_used...');

        $walletsWithNegativeLimits = Wallet::where('type', WalletTypeEnum::CARD_CREDIT)
            ->where('card_limit_used', '<', 0)
            ->get();

        if ($walletsWithNegativeLimits->isEmpty()) {
            $this->info('No wallets with negative limits found. All good! ✓');
            return Command::SUCCESS;
        }

        $this->warn("Found {$walletsWithNegativeLimits->count()} wallet(s) with negative limits.");

        $fixed = 0;
        foreach ($walletsWithNegativeLimits as $wallet) {
            $this->line("Fixing wallet: {$wallet->name} (ID: {$wallet->id})");
            $this->line("  Current limit_used: {$wallet->card_limit_used}");

            $wallet->update(['card_limit_used' => 0]);

            $this->info("  ✓ Fixed! New limit_used: 0");
            $fixed++;
        }

        $this->newLine();
        $this->info("Successfully fixed {$fixed} wallet(s)! ✓");

        return Command::SUCCESS;
    }
}
