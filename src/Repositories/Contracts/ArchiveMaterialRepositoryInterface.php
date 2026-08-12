<?php

namespace Modules\Glitter\Gallery\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Glitter\Gallery\Models\GalleryArchiveMaterial;

interface ArchiveMaterialRepositoryInterface
{
    public function getPublicPaginated(array $filters = []): LengthAwarePaginator;

    public function getPublicRecent(int $limit = 3): Collection;

    public function getPublicByCategory(string $category, array $filters = []): LengthAwarePaginator;

    public function findPublicByCategoryAndSlug(string $category, string $slug): ?GalleryArchiveMaterial;

    public function publicCategoryCounts(): array;
}
