<?php

namespace Modules\Glitter\Gallery\Enums;

enum GalleryItemType: string
{
    case Artwork = 'artwork';
    case Document = 'document';
    case Installation = 'installation';
    case Photo = 'photo';
    case Object = 'object';
    case Note = 'note';

    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
