<?php

namespace Modules\Glitter\Gallery\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Glitter\Gallery\Models\GalleryExhibition;

interface ExhibitionRepositoryInterface
{
    public function getPublicPaginated(array $filters = []): LengthAwarePaginator;

    public function getPublicFeaturedForHome(): ?GalleryExhibition;

    public function findPublicBySlug(string $slug): ?GalleryExhibition;
}
