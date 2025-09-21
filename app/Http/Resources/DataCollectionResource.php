<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class DataCollectionResource extends JsonResource
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
            'order' => $this->order,
            'pivot' => $this->whenPivotLoaded('data_collectionables', function () {
                return [
                    'key' => $this->pivot->key,
                    'order' => $this->pivot->order,
                    'paginate' => (bool) $this->pivot->paginate,
                ];
            }),
            'attributes' => $this->whenLoaded('attributes'),
            'images' => $this->whenLoaded('images'),
            'files' => $this->whenLoaded('files'),
            'template' => $this->whenLoaded('template'),
            'template_id' => $this->template_id,
            'link' => new LinkResource($this->whenLoaded('link')),
            'content_blocks' => ContentBlockResource::collection($this->whenLoaded('contentBlocks')),
            'data_entities' => DataEntityResource::collection(
                $this->whenLoaded('dataEntities', function () {
                    $this->loadDataEntityLinks();
                    return $this->dataEntities;
                })
            ),
            'children' => DataCollectionResource::collection(
                $this->whenLoaded('descendants', function ($entity) {
                    return $entity;
                })
            )
        ];
    }
}
