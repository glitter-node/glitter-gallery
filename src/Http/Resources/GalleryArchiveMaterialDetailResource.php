<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use Illuminate\Http\Request;

class GalleryArchiveMaterialDetailResource extends GalleryArchiveMaterialResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'description' => $this->field('description'),
            'related_items' => [],
            'related_records' => [],
        ];
    }
}
