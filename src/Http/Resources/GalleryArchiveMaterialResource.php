<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class GalleryArchiveMaterialResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->category?->value ?? $this->category,
            'category_label' => $this->category?->value ?? $this->category,
            'category_label_key' => $this->labelKey('gallery.archive.categories', $this->category?->value ?? $this->category, 'meta'),
            'type' => $this->type?->value ?? $this->type,
            'type_label' => $this->type?->value ?? $this->type,
            'type_label_key' => $this->labelKey('gallery.archive_materials.types', $this->type?->value ?? $this->type),
            'title' => $this->field('title'),
            'title_label' => $this->localized('title'),
            'subtitle' => $this->field('subtitle'),
            'date_label' => $this->date_label,
            'source' => $this->field('source'),
            'rights' => $this->field('rights'),
            'format' => $this->field('format'),
            'identifier' => $this->identifier,
            'media' => $this->media(),
            'summary' => $this->field('summary'),
            'summary_label' => $this->localized('summary'),
            'status' => $this->status?->value ?? $this->status,
            'visibility_state' => $this->visibility_state?->value ?? $this->visibility_state,
            'published_at' => $this->published_at?->toIso8601String(),
            'published_at_display' => $this->dateDisplay($this->published_at),
            'related_exhibition' => $this->whenLoaded('primaryExhibition', fn () => [
                'slug' => $this->primaryExhibition?->slug,
                'title' => is_array($this->primaryExhibition?->title) ? $this->primaryExhibition->title : [],
                'href' => $this->primaryExhibition ? '/exhibitions/'.$this->primaryExhibition->slug : null,
            ], null),
            'tags' => $this->relationLoaded('tags') ? GalleryTagResource::collection($this->tags)->resolve($request) : [],
            'href' => '/archive/'.($this->category?->value ?? $this->category).'/'.$this->slug,
        ];
    }

    protected function field(string $key): array
    {
        $value = $this->{$key};

        return is_array($value) ? $value : [];
    }

    protected function localized(string $key): string
    {
        $value = $this->field($key);
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'ko');

        return (string) ($value[$locale] ?? $value[$fallback] ?? (count($value) > 0 ? array_values($value)[0] : ''));
    }

    protected function labelKey(string $prefix, mixed $value, ?string $suffix = null): ?string
    {
        if (! $value) {
            return null;
        }

        return $prefix.'.'.str_replace('-', '_', (string) $value).($suffix ? '.'.$suffix : '');
    }

    protected function dateDisplay(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return app()->getLocale() === 'ko' ? $value->format('Y.m.d') : $value->format('M j, Y');
    }

    protected function media(): array
    {
        $value = $this->media;

        return is_array($value) ? $value : [
            'url' => null,
            'alt' => [],
            'credit' => null,
            'attachment_id' => null,
        ];
    }
}
