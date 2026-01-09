<?php

namespace App\Models;

use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory, HasUuidCustom;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'color',
    ];

    /**
     * Get the user that owns the tag.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions that have this tag.
     */
    public function transactions(): MorphToMany
    {
        return $this->morphedByMany(Transaction::class, 'taggable');
    }
}
