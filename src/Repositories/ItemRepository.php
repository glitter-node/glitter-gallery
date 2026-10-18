<?php

namespace Modules\Glitter\Gallery\Repositories;

use Illuminate\Support\Collection;
use Modules\Glitter\Gallery\Models\GalleryItem;
use Modules\Glitter\Gallery\Repositories\Concerns\AppliesPublicGalleryFilters;
use Modules\Glitter\Gallery\Repositories\Contracts\ItemRepositoryInterface;

class ItemRepository implements ItemRepositoryInterface
{
    use AppliesPublicGalleryFilters;

    public function getPublicForExhibition(int $exhibitionId, int $limit = 3): Collection
    {
        return $this->publicQuery(
            GalleryItem::query()->with(['exhibition', 'section', 'tags'])
        )
            ->where('exhibition_id', $exhibitionId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public function findPublicBySlug(string $slug): ?GalleryItem
    {
        return $this->publicQuery(
            GalleryItem::query()->with(['exhibition', 'section', 'tags'])
        )
            ->where('slug', $slug)
            ->first();
    }
}
