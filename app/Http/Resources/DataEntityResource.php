<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LinkResource;
use App\Http\Resources\ContentBlockResource;

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
            'order' => $this->order,
            'parent_id' => $this->parent_id,
            'template_id' => $this->template_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'images' => $this->whenLoaded('images'),
            'files' => $this->whenLoaded('files'),
            'attributes' => $this->whenLoaded('attributes'),
            'template' => $this->whenLoaded('template'),
            'variants' => DataEntityResource::collection($this->whenLoaded('variants')),
            'content_blocks' => ContentBlockResource::collection($this->whenLoaded('contentBlocks')),
            'audits' => $this->whenLoaded('audits'),
            'data_entityables' => $this->whenLoaded('dataEntityables'),
            'data_collections' => DataCollectionResource::collection($this->whenLoaded('dataCollections')),

            'pivot' => [
                'id' => $this->pivot->id ?? null,
                'key' => $this->pivot->key ?? null,
                'order' => $this->pivot->order ?? null,
                'link' => new LinkResource($this->pivot->link ?? null),
            ],
        ];
    }
}
