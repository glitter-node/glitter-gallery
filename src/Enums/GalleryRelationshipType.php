<?php

namespace Modules\Glitter\Gallery\Enums;

enum GalleryRelationshipType: string
{
    case ExhibitionItem = 'exhibition_item';
    case ItemRecord = 'item_record';
    case ItemArchiveMaterial = 'item_archive_material';
    case RecordArchiveMaterial = 'record_archive_material';
    case CollectionItem = 'collection_item';
    case ExhibitionRecord = 'exhibition_record';
    case ExhibitionArchiveMaterial = 'exhibition_archive_material';

    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
