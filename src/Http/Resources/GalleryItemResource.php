<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class GalleryItemResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'type' => $this->type?->value ?? $this->type,
            'type_label' => $this->type?->value ?? $this->type,
            'type_label_key' => $this->labelKey('gallery.items.types', $this->type?->value ?? $this->type),
            'title' => $this->field('title'),
            'title_label' => $this->localized('title'),
            'subtitle' => $this->field('subtitle'),
            'artist_name' => $this->field('artist_name'),
            'artist_label' => $this->localized('artist_name'),
            'year_label' => $this->year_label,
            'medium' => $this->field('medium'),
            'medium_label' => $this->localized('medium'),
            'dimensions' => $this->dimensions,
            'collection_name' => $this->field('collection_name'),
            'credit_line' => $this->field('credit_line'),
            'archive_category' => $this->archive_category?->value ?? $this->archive_category,
            'archive_category_label_key' => $this->labelKey('gallery.archive.categories', $this->archive_category?->value ?? $this->archive_category, 'meta'),
            'thumbnail_media' => $this->media('thumbnail_media'),
            'detail_media' => $this->media('detail_media'),
            'visual' => $this->media('detail_media'),
            'summary' => $this->field('summary'),
            'summary_label' => $this->localized('summary'),
            'curator_note' => $this->field('curator_note'),
            'status' => $this->status?->value ?? $this->status,
            'visibility_state' => $this->visibility_state?->value ?? $this->visibility_state,
            'published_at' => $this->published_at?->toIso8601String(),
            'published_at_display' => $this->dateDisplay($this->published_at),
            'exhibition_context' => $this->whenLoaded('exhibition', fn () => [
                'slug' => $this->exhibition?->slug,
                'title' => is_array($this->exhibition?->title) ? $this->exhibition->title : [],
                'href' => $this->exhibition ? '/exhibitions/'.$this->exhibition->slug : null,
            ], null),
            'tags' => $this->relationLoaded('tags') ? GalleryTagResource::collection($this->tags)->resolve($request) : [],
            'related_records' => [],
            'archive_materials' => [],
            'relationships' => [],
            'href' => '/items/'.$this->slug,
        ];
    }

    private function field(string $key): array
    {
        $value = $this->{$key};

        return is_array($value) ? $value : [];
    }

    private function localized(string $key): string
    {
        $value = $this->field($key);
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'ko');

        return (string) ($value[$locale] ?? $value[$fallback] ?? (count($value) > 0 ? array_values($value)[0] : ''));
    }

    private function labelKey(string $prefix, mixed $value, ?string $suffix = null): ?string
    {
        if (! $value) {
            return null;
        }

        return $prefix.'.'.str_replace('-', '_', (string) $value).($suffix ? '.'.$suffix : '');
    }

    private function dateDisplay(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return app()->getLocale() === 'ko' ? $value->format('Y.m.d') : $value->format('M j, Y');
    }

    private function media(string $key): array
    {
        $value = $this->{$key};

        return is_array($value) ? $value : [
            'url' => null,
            'alt' => [],
            'credit' => null,
            'attachment_id' => null,
        ];
    }
}
