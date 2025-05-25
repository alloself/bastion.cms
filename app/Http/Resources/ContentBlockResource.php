<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AttributeResource;

class ContentBlockResource extends JsonResource
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
            'content' => $this->content,
            'order' => $this->order,
            'parent_id' => $this->parent_id,
            'template_id' => $this->template_id,
            'children' => $this->whenLoaded('children'),
            'attributes' => $this->whenLoaded('attributes'),
            'template' => $this->whenLoaded('template'),
            'children' => self::collection($this->whenLoaded('children')),
            'images' => $this->whenLoaded('images'),
            'link' => $this->whenLoaded('link'),
            'data_entities' => $this->whenLoaded('dataEntities'),
            'data_collections' => DataCollectionResource::collection($this->whenLoaded('dataCollections')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parent' => $this->whenLoaded('parent'),
            'pivot' => $this->whenPivotLoaded('content_blockables', function () {
                return [
                    'order' => $this->pivot->order,
                    'key' => $this->pivot->key
                ];
            }),
        ];
    }
}
