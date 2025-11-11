<?php

namespace App\Models;

use App\Enums\WalletTypeEnum;
use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\WalletFactory> */
    use HasFactory, HasUuidCustom;

    protected $fillable = [
      'name',
      'uuid',
      'type',
      'day_close',
      'best_shopping_day',
      'card_limit',
      'card_limit_used',
      'initial_balance',
      'status'
    ];

    protected function casts(): array
    {
        return [
            'type' => WalletTypeEnum::class,
            'status' => 'boolean',
        ];
    }

    /**
     * @param $value
     * @return void
     */
    public function setCardLimitAttribute($value): void
    {
        $this->attributes['card_limit'] = $value * 100;
    }

    /**
     * @param $value
     * @return float|int
     */
    public function getCardLimitAttribute($value): float|int
    {
        return $value / 100;
    }

    public function setCardLimitUsedAttribute($value): void
    {
        $this->attributes['card_limit_used'] = $value * 100;
    }

    public function getCardLimitUsedAttribute($value): float|int
    {
        return $value / 100;
    }

    public function setInitialBalanceAttribute($value): void
    {
        $this->attributes['initial_balance'] = $value * 100;
    }

    public function getInitialBalanceAttribute($value): float|int
    {
        return $value / 100;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}
