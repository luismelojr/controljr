<?php

namespace App\Models;

use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasUuidCustom;

    protected $fillable = [
        'name',
        'uuid',
        'user_id',
        'is_default',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'status' => 'boolean'
        ];
    }

    public function user(): BelongsTo {
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

    public function incomeTransactions()
    {
        return $this->hasMany(IncomeTransaction::class);
    }
}
