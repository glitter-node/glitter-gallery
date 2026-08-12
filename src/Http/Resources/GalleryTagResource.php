<?php

namespace Modules\Glitter\Gallery\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class GalleryTagResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        $name = is_array($this->name) ? $this->name : [];

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $name,
            'name_label' => $this->localized($name),
            'taxonomy_group' => $this->taxonomy_group,
        ];
    }

    private function localized(array $value): string
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'ko');

        return (string) ($value[$locale] ?? $value[$fallback] ?? (count($value) > 0 ? array_values($value)[0] : ''));
    }
}
