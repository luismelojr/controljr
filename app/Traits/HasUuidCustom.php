<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuidCustom
{
    /**
     * Boot the trait.
     *
     * Automatically generate UUID when creating a new model instance.
     */
    protected static function bootHasUuidCustom(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * This allows using UUID in route model binding instead of ID.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Scope a query to find a model by UUID.
     */
    public function scopeWhereUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }
}
