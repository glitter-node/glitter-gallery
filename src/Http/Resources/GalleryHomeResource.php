<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class GalleryHomeResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'featured_exhibition' => $this->getValue('featured_exhibition'),
            'highlighted_items' => $this->getValue('highlighted_items', []),
            'recent_records' => $this->getValue('recent_records', []),
            'recent_archive_materials' => $this->getValue('recent_archive_materials', []),
            'relationship_paths' => $this->getValue('relationship_paths', []),
            'archive_categories' => $this->getValue('archive_categories', []),
            'about_context' => $this->getValue('about_context', []),
        ];
    }
}
