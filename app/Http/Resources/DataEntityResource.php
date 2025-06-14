<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LinkResource;

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
            'images' => $this->whenLoaded('images'),
            'files' => $this->whenLoaded('files'),
            'attributes' => $this->whenLoaded('attributes'),
            'template' => $this->whenLoaded('template'),
            'dataCollection' => $this->whenLoaded('dataCollection', 
                fn() => new DataCollectionResource($this->dataCollection)
            ),
            'dataCollectionLink' => $this->whenLoaded('dataCollectionLink',
                fn() => new LinkResource($this->dataCollectionLink)
            ),
            'pivot' => [
                'id' => $this->pivot->id ?? null,
                'key' => $this->pivot->key ?? null,
                'order' => $this->pivot->order ?? null,
                'link' => new LinkResource($this->pivot->link ?? null),
            ],
        ];
    }
}
