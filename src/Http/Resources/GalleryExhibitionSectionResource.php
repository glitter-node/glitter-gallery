<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class GalleryExhibitionSectionResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->field('title'),
            'title_label' => $this->localized('title'),
            'summary' => $this->field('summary'),
            'summary_label' => $this->localized('summary'),
            'body' => $this->field('body'),
            'insertion_point' => $this->insertion_point,
            'sort_order' => (int) $this->sort_order,
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
}
