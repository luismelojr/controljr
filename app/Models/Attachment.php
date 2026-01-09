<?php

namespace App\Models;

use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory, HasUuidCustom;

    protected $fillable = [
        'uuid',
        'user_id',
        'attachable_id',
        'attachable_type',
        'file_name',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'extension',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Accessors
     */
    protected function fileSizeFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatBytes($this->file_size),
        );
    }

    protected function downloadUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => route('dashboard.attachments.download', ['attachment' => $this->uuid]),
        );
    }

    protected function isImage(): Attribute
    {
        return Attribute::make(
            get: fn () => str_starts_with($this->mime_type, 'image/'),
        );
    }

    protected function isPdf(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->mime_type === 'application/pdf',
        );
    }

    /**
     * Methods
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function deleteFile(): void
    {
        if (Storage::disk('private')->exists($this->file_path)) {
            Storage::disk('private')->delete($this->file_path);
        }
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Delete file when model is deleted
        static::deleting(function ($attachment) {
            $attachment->deleteFile();
        });
    }
}
