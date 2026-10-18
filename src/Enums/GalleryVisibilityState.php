<?php

namespace Modules\Glitter\Gallery\Enums;

enum GalleryVisibilityState: string
{
    case Public = 'public';
    case Unlisted = 'unlisted';
    case Private = 'private';

    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
