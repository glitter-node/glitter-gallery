<?php

namespace Modules\Glitter\Gallery\Enums;

enum GalleryArchiveCategory: string
{
    case Exhibition = 'exhibition';
    case Artist = 'artist';
    case Material = 'material';
    case Document = 'document';
    case Research = 'research';
    case FieldRecord = 'field_record';

    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
