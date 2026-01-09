<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'attachable_id' => $this->attachable_id,
            'attachable_type' => $this->attachable_type,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'original_name' => $this->original_name,
            'file_size' => $this->file_size,
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            // Accessors
            'file_size_formatted' => $this->file_size_formatted,
            'download_url' => $this->download_url,
            'is_image' => $this->is_image,
            'is_pdf' => $this->is_pdf,
        ];
    }
}
