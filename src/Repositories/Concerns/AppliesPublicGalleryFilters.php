<?php

namespace Modules\Glitter\Gallery\Repositories\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Modules\Glitter\Gallery\Enums\GalleryPublicationStatus;
use Modules\Glitter\Gallery\Enums\GalleryVisibilityState;

trait AppliesPublicGalleryFilters
{
    protected function publicQuery(Builder|Relation $query): Builder|Relation
    {
        return $query
            ->where('status', GalleryPublicationStatus::Published->value)
            ->whereIn('visibility_state', [
                GalleryVisibilityState::Public->value,
                GalleryVisibilityState::Unlisted->value,
            ])
            ->where(function (Builder $query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    protected function perPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 12);

        return max(1, min($perPage, 60));
    }
}
