<?php

namespace Modules\Glitter\Gallery\Repositories\Contracts;

use Illuminate\Support\Collection;
use Modules\Glitter\Gallery\Models\GalleryItem;

interface ItemRepositoryInterface
{
    public function getPublicForExhibition(int $exhibitionId, int $limit = 3): Collection;

    public function findPublicBySlug(string $slug): ?GalleryItem;
}
