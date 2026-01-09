<?php

namespace App\Domain\Attachments\Services;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    /**
     * Store a new attachment
     */
    public function store(User $user, Model $attachable, UploadedFile $file): Attachment
    {
        // Generate unique file name
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;

        // Determine storage path based on attachable type
        $folder = $this->getFolderName($attachable);
        $path = "attachments/{$folder}/{$user->id}";

        // Store file
        $filePath = Storage::disk('private')->putFileAs($path, $file, $fileName);

        // Create attachment record
        return Attachment::create([
            'user_id' => $user->id,
            'attachable_id' => $attachable->id,
            'attachable_type' => get_class($attachable),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
        ]);
    }

    /**
     * Delete an attachment
     */
    public function delete(Attachment $attachment): bool
    {
        return $attachment->delete();
    }

    /**
     * Get user's attachments with optional filtering
     */
    public function getUserAttachments(User $user, ?string $type = null)
    {
        $query = Attachment::where('user_id', $user->id)
            ->with('attachable')
            ->orderBy('created_at', 'desc');

        if ($type) {
            $query->where('attachable_type', $type);
        }

        return $query->get();
    }

    /**
     * Get attachments for a specific model
     */
    public function getAttachmentsFor(Model $model)
    {
        return Attachment::where('attachable_id', $model->id)
            ->where('attachable_type', get_class($model))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get total storage used by user
     */
    public function getUserTotalStorage(User $user): int
    {
        return Attachment::where('user_id', $user->id)
            ->sum('file_size');
    }

    /**
     * Get user's attachment count
     */
    public function getUserAttachmentCount(User $user): int
    {
        return Attachment::where('user_id', $user->id)->count();
    }

    /**
     * Check if user can upload more attachments
     */
    public function canUpload(User $user): bool
    {
        $limit = $user->getPlanLimits()['max_attachments'] ?? 0;

        // -1 means unlimited
        if ($limit === -1) {
            return true;
        }

        // 0 means feature disabled
        if ($limit === 0) {
            return false;
        }

        return $this->getUserAttachmentCount($user) < $limit;
    }

    /**
     * Validate file type
     */
    public function isValidFileType(UploadedFile $file): bool
    {
        $allowedMimes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'text/csv',
        ];

        return in_array($file->getMimeType(), $allowedMimes);
    }

    /**
     * Validate file size (max 5MB)
     */
    public function isValidFileSize(UploadedFile $file): bool
    {
        $maxSize = 5 * 1024 * 1024; // 5MB in bytes
        return $file->getSize() <= $maxSize;
    }

    /**
     * Get folder name based on attachable type
     */
    protected function getFolderName(Model $model): string
    {
        return match (class_basename($model)) {
            'Transaction' => 'transactions',
            'Income' => 'incomes',
            'IncomeTransaction' => 'income-transactions',
            default => 'other',
        };
    }
}
