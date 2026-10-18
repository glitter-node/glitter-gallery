<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class GalleryArchiveCategoryResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->getValue('slug'),
            'category' => $this->getValue('category'),
            'label' => $this->getValue('label'),
            'label_key' => 'gallery.archive.categories.'.str_replace('-', '_', (string) $this->getValue('slug')).'.title',
            'meta_key' => 'gallery.archive.categories.'.str_replace('-', '_', (string) $this->getValue('slug')).'.meta',
            'body_key' => 'gallery.archive.categories.'.str_replace('-', '_', (string) $this->getValue('slug')).'.body',
            'summary' => $this->getValue('summary', []),
            'materials_count' => (int) $this->getValue('materials_count', 0),
            'href' => '/archive/'.$this->getValue('slug'),
        ];
    }
}
