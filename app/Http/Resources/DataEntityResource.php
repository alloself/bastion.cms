<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataEntityResource extends JsonResource
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
            'name' => $this->name,
            'meta' => $this->meta,
            'content' => $this->content,
            'images' => $this->images,
            'files' => $this->files,
            'attributes' => $this->attributes,
            'template' => $this->template,
            'pivot' => [
                'id' => $this->pivot->id ?? null,
                'key' => $this->pivot->key ?? null,
                'order' => $this->pivot->order ?? null,
                'link' => new LinkResource($this->pivot->link ?? null),
            ],
        ];
    }
}
