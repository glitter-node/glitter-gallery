<?php

namespace Modules\Glitter\Gallery\Enums;

enum GalleryRecordType: string
{
    case CuratorNote = 'curator_note';
    case FieldNote = 'field_note';
    case InstallationNote = 'installation_note';
    case ProcessNote = 'process_note';
    case ResearchNote = 'research_note';

    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
