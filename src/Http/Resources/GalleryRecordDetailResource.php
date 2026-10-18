<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use Illuminate\Http\Request;

class GalleryRecordDetailResource extends GalleryRecordResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'body_sections' => $this->field('body_sections'),
            'related_items' => [],
            'archive_materials' => [],
        ];
    }
}
