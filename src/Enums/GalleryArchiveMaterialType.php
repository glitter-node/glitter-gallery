<?php

namespace Modules\Glitter\Gallery\Enums;

enum GalleryArchiveMaterialType: string
{
    case Image = 'image';
    case Document = 'document';
    case Note = 'note';
    case InstallationPhoto = 'installation_photo';
    case Interview = 'interview';
    case Press = 'press';
    case ObjectReference = 'object_reference';

    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
