<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuidCustom;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'uuid',
        'email',
        'cpf',
        'google_id',
        'password',
        'phone',
        'status',
        'current_subscription_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    /**
     * Get the wallets for the user.
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Get the categories for the user.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get the accounts for the user.
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the incomes for the user.
     */
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    /**
     * Get the income transactions for the user.
     */
    public function incomeTransactions()
    {
        return $this->hasMany(IncomeTransaction::class);
    }

    /**
     * Get the alerts for the user.
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Get the alert notifications for the user.
     */
    public function alertNotifications()
    {
        return $this->hasMany(AlertNotification::class);
    }

    /**
     * Get all subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the current active subscription.
     */
    public function currentSubscription()
    {
        return $this->belongsTo(Subscription::class, 'current_subscription_id');
    }

    /**
     * Check if user has an active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->currentSubscription?->isActive() ?? false;
    }

    /**
     * Check if user is on a specific plan
     */
    public function isOnPlan(string $slug): bool
    {
        return $this->currentSubscription?->plan?->slug === $slug;
    }

    /**
     * Check if user is on free plan
     */
    public function isOnFreePlan(): bool
    {
        return $this->currentSubscription === null
            || $this->isOnPlan('free');
    }

    /**
     * Check if user is on premium plan
     */
    public function isOnPremiumPlan(): bool
    {
        return $this->isOnPlan('premium');
    }

    /**
     * Check if user is on family plan
     */
    public function isOnFamilyPlan(): bool
    {
        return $this->isOnPlan('family');
    }

    /**
     * Get plan limits for current subscription
     */
    public function getPlanLimits(): array
    {
        if (! $this->currentSubscription || ! $this->currentSubscription->plan) {
            return config('plan_limits.free', []);
        }

        $planSlug = $this->currentSubscription->plan->slug;

        return config("plan_limits.{$planSlug}", config('plan_limits.free', []));
    }

    /**
     * Check if user can use a feature
     */
    public function canUseFeature(string $feature): bool
    {
        $limits = $this->getPlanLimits();

        if (! isset($limits[$feature])) {
            return true; // Se não tem limite definido, permite
        }

        // Se é boolean
        if (is_bool($limits[$feature])) {
            return $limits[$feature];
        }

        // Se é -1, é ilimitado
        if ($limits[$feature] === -1) {
            return true;
        }

        return false;
    }

    /**
     * Get current subscription plan
     */
    public function plan()
    {
        return $this->currentSubscription?->plan;
    }
}
