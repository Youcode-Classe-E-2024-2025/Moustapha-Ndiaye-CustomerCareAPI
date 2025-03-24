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
            'filename' => $this->filename,
            'file_size' => $this->file_size,
            'mime_type' => $this->mime_type,
            'uploaded_by' => new UserResource($this->whenLoaded('uploader')),
            'attachable_type' => $this->attachable_type,
            'attachable_id' => $this->attachable_id,
            'created_at' => $this->created_at,
        ];
    }
}