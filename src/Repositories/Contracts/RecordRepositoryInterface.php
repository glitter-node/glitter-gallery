<?php

namespace Modules\Glitter\Gallery\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Glitter\Gallery\Models\GalleryRecord;

interface RecordRepositoryInterface
{
    public function getPublicPaginated(array $filters = []): LengthAwarePaginator;

    public function getPublicRecent(int $limit = 3): Collection;

    public function findPublicBySlug(string $slug): ?GalleryRecord;
}
