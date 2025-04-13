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
            'attributes' => $this->attributes,
            'images' => $this->images,
            'files' => $this->files,
            'link' => new LinkResource($this->whenLoaded('link')),
            'data_entities' => DataEntityResource::collection(
                $this->whenLoaded('dataEntities', function () {
                    $this->loadDataEntityLinks();
                    return $this->dataEntities;
                })
            ),
            'children' => DataCollectionResource::collection(
                $this->whenLoaded('children')
            ),
        ];
    }
}
