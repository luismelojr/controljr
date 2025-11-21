<?php

namespace App\Models;

use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory, HasUuidCustom, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'amount',
        'period',
        'recurrence',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'period' => 'date',
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
