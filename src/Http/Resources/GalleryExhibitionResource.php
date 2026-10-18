<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class GalleryExhibitionResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->field('title'),
            'title_label' => $this->localized('title'),
            'subtitle' => $this->field('subtitle'),
            'subtitle_label' => $this->localized('subtitle'),
            'summary' => $this->field('summary'),
            'summary_label' => $this->localized('summary'),
            'period_label' => $this->field('period_label'),
            'starts_at' => $this->starts_at?->toIso8601String(),
            'starts_at_display' => $this->dateDisplay($this->starts_at),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'ends_at_display' => $this->dateDisplay($this->ends_at),
            'location' => $this->field('location'),
            'location_label' => $this->localized('location'),
            'featured_image' => $this->media('featured_image'),
            'is_featured' => (bool) $this->is_featured,
            'status' => $this->status?->value ?? $this->status,
            'visibility_state' => $this->visibility_state?->value ?? $this->visibility_state,
            'published_at' => $this->published_at?->toIso8601String(),
            'published_at_display' => $this->dateDisplay($this->published_at),
            'tags' => $this->relationLoaded('tags') ? GalleryTagResource::collection($this->tags)->resolve($request) : [],
            'href' => '/exhibitions/'.$this->slug,
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

    protected function dateDisplay(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return app()->getLocale() === 'ko' ? $value->format('Y.m.d') : $value->format('M j, Y');
    }

    protected function media(string $key): array
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
