<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => (int)$this->id,
            'course_id'   => (int)$this->course_id,
            'name'        => $this->title,
            'filename'    => basename($this->file_path),
            'size'        => (int)($this->size_bytes ?? 0),
            'mime'        => $this->mime_type,
            'uploaded_at' => optional($this->created_at)->toIso8601String(),
            'download_count' => (int)($this->download_count ?? 0),
            'course'      => $this->whenLoaded('course', fn() => [
                'id'   => (int)$this->course->id,
                'name' => $this->course->name ?? null,
            ]),
        ];
    }
}
