<?php

namespace App\Models;

use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedReport extends Model
{
    use HasUuidCustom;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'description',
        'report_type',
        'filters',
        'visualization',
        'is_template',
        'is_favorite',
        'last_run_at',
        'run_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'visualization' => 'array',
            'is_template' => 'boolean',
            'is_favorite' => 'boolean',
            'last_run_at' => 'datetime',
            'run_count' => 'integer',
        ];
    }

    /**
     * Get the user that owns the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter favorite reports.
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope to filter user reports (non-templates).
     */
    public function scopeUserReports($query)
    {
        return $query->where('is_template', false);
    }

    /**
     * Scope to filter template reports.
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    /**
     * Increment run count and update last run time.
     */
    public function incrementRunCount(): void
    {
        $this->increment('run_count');
        $this->update(['last_run_at' => now()]);
    }
}
