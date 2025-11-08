<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Wallet;
use App\Policies\CategoryPolicy;
use App\Policies\WalletPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        // Register model policies
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Wallet::class, WalletPolicy::class);
    }
}
