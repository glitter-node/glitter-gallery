<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use Illuminate\Http\Request;

class GalleryExhibitionDetailResource extends GalleryExhibitionResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'curator_note' => $this->field('curator_note'),
            'metadata' => $this->field('meta'),
            'sections' => GalleryExhibitionSectionResource::collection($this->whenLoaded('sections'))->resolve($request),
            'items' => GalleryItemResource::collection($this->whenLoaded('items'))->resolve($request),
            'related_records' => [],
            'archive_materials' => [],
            'related_collections' => [],
            'timeline_references' => [],
        ];
    }
}
