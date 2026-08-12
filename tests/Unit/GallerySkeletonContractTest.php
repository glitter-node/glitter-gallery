<?php

namespace Modules\Glitter\Gallery\Tests\Unit;

use Modules\Glitter\Gallery\Enums\GalleryArchiveCategory;
use Modules\Glitter\Gallery\Enums\GalleryArchiveMaterialType;
use Modules\Glitter\Gallery\Enums\GalleryItemType;
use Modules\Glitter\Gallery\Enums\GalleryPublicationStatus;
use Modules\Glitter\Gallery\Enums\GalleryRecordType;
use Modules\Glitter\Gallery\Enums\GalleryRelationshipType;
use Modules\Glitter\Gallery\Enums\GalleryVisibilityState;
use Modules\Glitter\Gallery\Models\GalleryArchiveMaterial;
use Modules\Glitter\Gallery\Models\GalleryCollection;
use Modules\Glitter\Gallery\Models\GalleryExhibition;
use Modules\Glitter\Gallery\Models\GalleryExhibitionSection;
use Modules\Glitter\Gallery\Models\GalleryItem;
use Modules\Glitter\Gallery\Models\GalleryRecord;
use Modules\Glitter\Gallery\Models\GalleryRelationship;
use Modules\Glitter\Gallery\Models\GalleryTag;
use PHPUnit\Framework\TestCase;

class GallerySkeletonContractTest extends TestCase
{
    public function test_migration_files_exist_for_required_gallery_tables(): void
    {
        foreach ($this->requiredMigrations() as $migration) {
            $this->assertFileExists($this->modulePath('database/migrations/'.$migration));
        }
    }

    public function test_model_classes_exist_for_required_gallery_models(): void
    {
        foreach ([
            GalleryExhibition::class,
            GalleryExhibitionSection::class,
            GalleryItem::class,
            GalleryRecord::class,
            GalleryArchiveMaterial::class,
            GalleryCollection::class,
            GalleryTag::class,
            GalleryRelationship::class,
        ] as $class) {
            $this->assertTrue(class_exists($class), "{$class} must exist");
        }
    }

    public function test_enum_classes_include_expected_values(): void
    {
        $this->assertSame(['draft', 'published', 'private', 'archived'], GalleryPublicationStatus::values());
        $this->assertSame(['public', 'unlisted', 'private'], GalleryVisibilityState::values());
        $this->assertSame(['artwork', 'document', 'installation', 'photo', 'object', 'note'], GalleryItemType::values());
        $this->assertSame(['curator_note', 'field_note', 'installation_note', 'process_note', 'research_note'], GalleryRecordType::values());
        $this->assertSame(['image', 'document', 'note', 'installation_photo', 'interview', 'press', 'object_reference'], GalleryArchiveMaterialType::values());
        $this->assertSame(['exhibition', 'artist', 'material', 'document', 'research', 'field_record'], GalleryArchiveCategory::values());
        $this->assertSame(['exhibition_item', 'item_record', 'item_archive_material', 'record_archive_material', 'collection_item', 'exhibition_record', 'exhibition_archive_material'], GalleryRelationshipType::values());
    }

    public function test_migration_files_are_php_lint_valid_without_running_migrations(): void
    {
        foreach ($this->requiredMigrations() as $migration) {
            $file = $this->modulePath('database/migrations/'.$migration);
            $output = [];
            $exitCode = 0;

            exec('/usr/local/bin/php83 -l '.escapeshellarg($file), $output, $exitCode);

            $this->assertSame(0, $exitCode, implode("\n", $output));
        }
    }

    public function test_contract_explicitly_keeps_this_phase_migration_free(): void
    {
        $contract = file_get_contents(dirname(__DIR__, 5).'/docs/gallery-platform-schema-contract.md');

        $this->assertStringContainsString('No migration should be run during this contract phase.', $contract);
        $this->assertStringContainsString('no production migration execution', $contract);
    }

    private function requiredMigrations(): array
    {
        return [
            '2026_05_10_000001_create_gallery_exhibitions_table.php',
            '2026_05_10_000002_create_gallery_exhibition_sections_table.php',
            '2026_05_10_000003_create_gallery_items_table.php',
            '2026_05_10_000004_create_gallery_records_table.php',
            '2026_05_10_000005_create_gallery_archive_materials_table.php',
            '2026_05_10_000006_create_gallery_collections_table.php',
            '2026_05_10_000007_create_gallery_tags_table.php',
            '2026_05_10_000008_create_gallery_taggables_table.php',
            '2026_05_10_000009_create_gallery_relationships_table.php',
        ];
    }

    private function modulePath(string $path): string
    {
        return dirname(__DIR__, 2).'/'.$path;
    }
}
