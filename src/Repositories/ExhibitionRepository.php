<?php

namespace Modules\Glitter\Gallery\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Glitter\Gallery\Models\GalleryExhibition;
use Modules\Glitter\Gallery\Repositories\Concerns\AppliesPublicGalleryFilters;
use Modules\Glitter\Gallery\Repositories\Contracts\ExhibitionRepositoryInterface;

class ExhibitionRepository implements ExhibitionRepositoryInterface
{
    use AppliesPublicGalleryFilters;

    public function getPublicPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = $this->publicQuery(
            GalleryExhibition::query()->with(['tags'])
        );

        if (($filters['featured'] ?? null) !== null) {
            $query->where('is_featured', filter_var($filters['featured'], FILTER_VALIDATE_BOOL));
        }

        return $query
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($this->perPage($filters));
    }

    public function getPublicFeaturedForHome(): ?GalleryExhibition
    {
        $query = $this->publicQuery(
            GalleryExhibition::query()->with(['tags'])
        );

        return (clone $query)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first()
            ?? $query
                ->orderBy('sort_order')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->first();
    }

    public function findPublicBySlug(string $slug): ?GalleryExhibition
    {
        return $this->publicQuery(
            GalleryExhibition::query()->with([
                'sections' => fn ($query) => $this->publicQuery($query)->orderBy('sort_order')->orderBy('id'),
                'items' => fn ($query) => $this->publicQuery($query)->orderBy('sort_order')->orderBy('id'),
                'tags',
            ])
        )
            ->where('slug', $slug)
            ->first();
    }
}
