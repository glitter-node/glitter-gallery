<?php

namespace Modules\Glitter\Gallery\Enums;

enum GalleryPublicationStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Private = 'private';
    case Archived = 'archived';

    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
