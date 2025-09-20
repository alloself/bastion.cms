<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'index' => $this->index,
            'meta' => $this->meta,
            'parent_id' => $this->parent_id,
            'template_id' => $this->template_id,
            'template' => new TemplateResource($this->whenLoaded('template')),
            'parent' => new PageResource($this->whenLoaded('parent')),
            'link' => new LinkResource($this->whenLoaded('link')),
            'content_blocks' => ContentBlockResource::collection($this->whenLoaded('contentBlocks')),
            'attributes' => $this->attributes,
            'images' => $this->images,
            'data_collections' => DataCollectionResource::collection($this->whenLoaded('dataCollections')),
            'depth' => $this->depth,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
