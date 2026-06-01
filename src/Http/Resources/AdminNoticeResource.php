<?php

namespace Ssh521\LaravelAdmin\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminNoticeResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toIso8601String(),
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                    'email' => $this->author->email,
                ];
            }),
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'original_name' => $attachment->original_name,
                        'file_path' => $attachment->file_path,
                        'file_url' => $attachment->url,
                        'file_size' => $attachment->file_size,
                        'formatted_size' => $attachment->formatted_size,
                        'mime_type' => $attachment->mime_type,
                        'sort_order' => $attachment->sort_order,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
