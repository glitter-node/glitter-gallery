<?php

namespace Modules\Glitter\Gallery\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Glitter\Gallery\Enums\GalleryArchiveCategory;
use Modules\Glitter\Gallery\Http\Resources\GalleryArchiveCategoryResource;
use Modules\Glitter\Gallery\Http\Resources\GalleryArchiveMaterialDetailResource;
use Modules\Glitter\Gallery\Http\Resources\GalleryArchiveMaterialResource;
use Modules\Glitter\Gallery\Http\Resources\GalleryExhibitionDetailResource;
use Modules\Glitter\Gallery\Http\Resources\GalleryExhibitionResource;
use Modules\Glitter\Gallery\Http\Resources\GalleryHomeResource;
use Modules\Glitter\Gallery\Http\Resources\GalleryItemResource;
use Modules\Glitter\Gallery\Http\Resources\GalleryRecordDetailResource;
use Modules\Glitter\Gallery\Http\Resources\GalleryRecordResource;
use Modules\Glitter\Gallery\Models\GalleryRelationship;
use Modules\Glitter\Gallery\Repositories\Contracts\ArchiveMaterialRepositoryInterface;
use Modules\Glitter\Gallery\Repositories\Contracts\ExhibitionRepositoryInterface;
use Modules\Glitter\Gallery\Repositories\Contracts\ItemRepositoryInterface;
use Modules\Glitter\Gallery\Repositories\Contracts\RecordRepositoryInterface;

class PublicGalleryService
{
    public function __construct(
        private ExhibitionRepositoryInterface $exhibitions,
        private ItemRepositoryInterface $items,
        private RecordRepositoryInterface $records,
        private ArchiveMaterialRepositoryInterface $archiveMaterials,
    ) {}

    public function home(): array
    {
        $featured = $this->exhibitions->getPublicFeaturedForHome();
        $items = $featured ? $this->items->getPublicForExhibition($featured->id, 3) : collect();
        $records = $this->records->getPublicRecent(3);
        $materials = $this->archiveMaterials->getPublicRecent(3);
        $featuredPayload = $featured ? (new GalleryExhibitionResource($featured))->resolve(request()) : null;
        $itemPayloads = GalleryItemResource::collection($items)->resolve(request());
        $recordPayloads = GalleryRecordResource::collection($records)->resolve(request());
        $materialPayloads = GalleryArchiveMaterialResource::collection($materials)->resolve(request());
        $archive = $this->archive();

        return (new GalleryHomeResource([
            'featured_exhibition' => $featuredPayload,
            'highlighted_items' => $itemPayloads,
            'recent_records' => $recordPayloads,
            'recent_archive_materials' => $materialPayloads,
            'relationship_paths' => $this->relationshipPaths($featuredPayload, $itemPayloads, $recordPayloads, $materialPayloads),
            'archive_categories' => $archive['categories'],
            'about_context' => [
                'kicker_key' => 'gallery.home.about_compact.kicker',
                'title_key' => 'gallery.home.about_compact.title',
                'body_key' => 'gallery.home.about_compact.body',
                'href' => '/about',
            ],
        ]))->resolve(request());
    }

    public function exhibitions(array $filters = []): array
    {
        $paginator = $this->exhibitions->getPublicPaginated($filters);
        $items = GalleryExhibitionResource::collection($paginator->getCollection())->resolve(request());

        return [
            'data' => $items,
            'featured' => $items[0] ?? null,
            'groups' => [],
            'filters' => [
                'featured' => $filters['featured'] ?? null,
            ],
            'pagination' => $this->pagination($paginator),
        ];
    }

    public function exhibition(string $slug): ?array
    {
        $exhibition = $this->exhibitions->findPublicBySlug($slug);

        return $exhibition ? (new GalleryExhibitionDetailResource($exhibition))->resolve(request()) : null;
    }

    public function item(string $slug): ?array
    {
        $item = $this->items->findPublicBySlug($slug);

        return $item ? (new GalleryItemResource($item))->resolve(request()) : null;
    }

    public function records(array $filters = []): array
    {
        $paginator = $this->records->getPublicPaginated($filters);

        return [
            'data' => GalleryRecordResource::collection($paginator->getCollection())->resolve(request()),
            'record_types' => [],
            'filters' => [
                'type' => $filters['type'] ?? null,
            ],
            'pagination' => $this->pagination($paginator),
        ];
    }

    public function record(string $slug): ?array
    {
        $record = $this->records->findPublicBySlug($slug);

        return $record ? (new GalleryRecordDetailResource($record))->resolve(request()) : null;
    }

    public function archive(): array
    {
        $counts = $this->archiveMaterials->publicCategoryCounts();
        $categories = array_map(
            fn (GalleryArchiveCategory $category): array => $this->categoryPayload($category, $counts[$category->value] ?? 0),
            GalleryArchiveCategory::cases()
        );

        return [
            'categories' => GalleryArchiveCategoryResource::collection($categories)->resolve(request()),
            'total_materials' => array_sum($counts),
            'filters' => [
                'categories' => GalleryArchiveCategory::values(),
            ],
        ];
    }

    public function archiveCategory(string $category, array $filters = []): ?array
    {
        $categoryEnum = GalleryArchiveCategory::tryFrom($category);

        if (! $categoryEnum) {
            return null;
        }

        $paginator = $this->archiveMaterials->getPublicByCategory($category, $filters);
        $counts = $this->archiveMaterials->publicCategoryCounts();

        return [
            'category' => (new GalleryArchiveCategoryResource($this->categoryPayload($categoryEnum, $counts[$category] ?? 0)))->resolve(request()),
            'materials' => GalleryArchiveMaterialResource::collection($paginator->getCollection())->resolve(request()),
            'type_summary' => [],
            'filters' => [
                'category' => $category,
                'type' => $filters['type'] ?? null,
            ],
            'pagination' => $this->pagination($paginator),
        ];
    }

    public function archiveMaterial(string $category, string $slug): ?array
    {
        if (! GalleryArchiveCategory::isValid($category)) {
            return null;
        }

        $material = $this->archiveMaterials->findPublicByCategoryAndSlug($category, $slug);

        return $material ? (new GalleryArchiveMaterialDetailResource($material))->resolve(request()) : null;
    }

    private function pagination(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    private function categoryPayload(GalleryArchiveCategory $category, int $count): array
    {
        return [
            'slug' => $category->value,
            'category' => $category->value,
            'label' => $category->value,
            'summary' => [],
            'materials_count' => $count,
        ];
    }

    private function relationshipPaths(?array $featured, array $items, array $records, array $materials): array
    {
        $entities = $this->homeEntityMap($featured, $items, $records, $materials);
        $paths = GalleryRelationship::query()
            ->ordered()
            ->get()
            ->map(function (GalleryRelationship $relationship) use ($entities): ?array {
                $source = $entities[$relationship->source_type.':'.$relationship->source_id] ?? null;
                $target = $entities[$relationship->target_type.':'.$relationship->target_id] ?? null;

                if (! $source || ! $target) {
                    return null;
                }

                return [
                    'id' => (string) $relationship->id,
                    'type' => $relationship->type?->value ?? $relationship->type,
                    'role' => $relationship->role,
                    'href' => $target['href'],
                    'title_label' => $source['label'].' → '.$target['label'],
                    'body_label' => $this->localized($relationship->note),
                    'from_label' => $source['label'],
                    'to_label' => $target['label'],
                    'source' => $source,
                    'target' => $target,
                ];
            })
            ->filter()
            ->take(3)
            ->values()
            ->all();

        if ($paths !== []) {
            return $this->indexPaths($paths);
        }

        return $this->indexPaths($this->fallbackRelationshipPaths($featured, $items, $records, $materials));
    }

    private function homeEntityMap(?array $featured, array $items, array $records, array $materials): array
    {
        $entities = [];

        if ($featured) {
            $entities['exhibition:'.$featured['id']] = $this->homeEntity('exhibition', $featured);
        }

        foreach ($items as $item) {
            $entities['item:'.$item['id']] = $this->homeEntity('item', $item);
        }

        foreach ($records as $record) {
            $entities['record:'.$record['id']] = $this->homeEntity('record', $record);
        }

        foreach ($materials as $material) {
            $entities['archive_material:'.$material['id']] = $this->homeEntity('archive_material', $material);
        }

        return $entities;
    }

    private function homeEntity(string $type, array $payload): array
    {
        return [
            'type' => $type,
            'id' => $payload['id'] ?? null,
            'slug' => $payload['slug'] ?? null,
            'label' => $payload['title_label'] ?? $payload['slug'] ?? '',
            'href' => $payload['href'] ?? '/',
        ];
    }

    private function fallbackRelationshipPaths(?array $featured, array $items, array $records, array $materials): array
    {
        $paths = [];

        if ($featured && ($records[0] ?? null)) {
            $paths[] = $this->fallbackPath('exhibition_record', $featured, $records[0]);
        }

        if (($items[0] ?? null) && ($materials[0] ?? null)) {
            $paths[] = $this->fallbackPath('item_archive_material', $items[0], $materials[0]);
        }

        if (($records[0] ?? null) && ($materials[0] ?? null)) {
            $paths[] = $this->fallbackPath('record_archive_material', $records[0], $materials[0]);
        }

        return array_slice($paths, 0, 3);
    }

    private function fallbackPath(string $type, array $source, array $target): array
    {
        $sourceLabel = $source['title_label'] ?? $source['slug'] ?? '';
        $targetLabel = $target['title_label'] ?? $target['slug'] ?? '';

        return [
            'id' => $type,
            'type' => $type,
            'role' => 'curated',
            'href' => $target['href'] ?? '/',
            'title_label' => $sourceLabel.' → '.$targetLabel,
            'body_label' => '',
            'from_label' => $sourceLabel,
            'to_label' => $targetLabel,
            'source' => $this->homeEntity('source', $source),
            'target' => $this->homeEntity('target', $target),
        ];
    }

    private function indexPaths(array $paths): array
    {
        return array_values(array_map(
            fn (array $path, int $index): array => ['index' => sprintf('%02d', $index + 1)] + $path,
            $paths,
            array_keys($paths)
        ));
    }

    private function localized(?array $value): string
    {
        if (! $value) {
            return '';
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'ko');

        return (string) ($value[$locale] ?? $value[$fallback] ?? (count($value) > 0 ? array_values($value)[0] : ''));
    }
}
