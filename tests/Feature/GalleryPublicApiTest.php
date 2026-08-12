<?php

namespace Modules\Glitter\Gallery\Tests\Feature;

require_once __DIR__.'/../ModuleTestCase.php';
require_once dirname(__DIR__, 2).'/database/seeders/GallerySampleSeeder.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Modules\Glitter\Gallery\Enums\GalleryArchiveCategory;
use Modules\Glitter\Gallery\Enums\GalleryArchiveMaterialType;
use Modules\Glitter\Gallery\Enums\GalleryItemType;
use Modules\Glitter\Gallery\Enums\GalleryPublicationStatus;
use Modules\Glitter\Gallery\Enums\GalleryRecordType;
use Modules\Glitter\Gallery\Enums\GalleryRelationshipType;
use Modules\Glitter\Gallery\Enums\GalleryVisibilityState;
use Modules\Glitter\Gallery\Database\Seeders\GallerySampleSeeder;
use Modules\Glitter\Gallery\Models\GalleryArchiveMaterial;
use Modules\Glitter\Gallery\Models\GalleryExhibition;
use Modules\Glitter\Gallery\Models\GalleryItem;
use Modules\Glitter\Gallery\Models\GalleryRecord;
use Modules\Glitter\Gallery\Models\GalleryRelationship;
use Modules\Glitter\Gallery\Tests\ModuleTestCase;

class GalleryPublicApiTest extends ModuleTestCase
{
    public function test_public_gallery_routes_are_registered(): void
    {
        foreach ([
            'api/gallery/home',
            'api/gallery/exhibitions',
            'api/gallery/exhibitions/sample-slug',
            'api/gallery/items/sample-slug',
            'api/gallery/records',
            'api/gallery/records/sample-slug',
            'api/gallery/archive',
            'api/gallery/archive/exhibition',
            'api/gallery/archive/exhibition/sample-slug',
        ] as $uri) {
            $this->assertNotNull(Route::getRoutes()->match(request()->create($uri, 'GET')));
        }
    }

    public function test_public_gallery_home_has_no_write_route(): void
    {
        $routes = collect(Route::getRoutes())->filter(fn ($route): bool => $route->uri() === 'api/gallery/home');

        $this->assertCount(1, $routes);
        $this->assertSame(['GET', 'HEAD'], array_values($routes->first()->methods()));
    }

    public function test_exhibitions_index_returns_empty_layout_shape(): void
    {
        $this->clearGalleryData();

        $this->getJson('/api/gallery/exhibitions')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data',
                    'featured',
                    'groups',
                    'filters',
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                        'from',
                        'to',
                    ],
                ],
            ])
            ->assertJsonPath('data.data', [])
            ->assertJsonPath('data.featured', null);
    }

    public function test_records_index_returns_empty_layout_shape(): void
    {
        $this->clearGalleryData();

        $this->getJson('/api/gallery/records')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'data',
                    'record_types',
                    'filters',
                    'pagination',
                ],
            ])
            ->assertJsonPath('data.data', []);
    }

    public function test_archive_index_returns_empty_category_collection_shape(): void
    {
        $this->clearGalleryData();

        $this->getJson('/api/gallery/archive')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'categories' => [
                        '*' => [
                            'slug',
                            'category',
                            'label',
                            'summary',
                            'materials_count',
                            'href',
                        ],
                    ],
                    'total_materials',
                    'filters',
                ],
            ])
            ->assertJsonPath('data.total_materials', 0);
    }

    public function test_home_returns_empty_safe_shape(): void
    {
        $this->clearGalleryData();

        $this->getJson('/api/gallery/home')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'featured_exhibition',
                    'highlighted_items',
                    'recent_records',
                    'recent_archive_materials',
                    'relationship_paths',
                    'archive_categories',
                    'about_context',
                ],
            ])
            ->assertJsonPath('data.featured_exhibition', null)
            ->assertJsonPath('data.highlighted_items', [])
            ->assertJsonPath('data.recent_records', [])
            ->assertJsonPath('data.recent_archive_materials', [])
            ->assertJsonPath('data.relationship_paths', [])
            ->assertJsonCount(6, 'data.archive_categories');
    }

    public function test_missing_detail_routes_return_404(): void
    {
        $this->clearGalleryData();

        $this->getJson('/api/gallery/exhibitions/missing-exhibition')->assertNotFound();
        $this->getJson('/api/gallery/items/missing-item')->assertNotFound();
        $this->getJson('/api/gallery/records/missing-record')->assertNotFound();
        $this->getJson('/api/gallery/archive/exhibition/missing-material')->assertNotFound();
    }

    public function test_draft_and_private_records_are_not_returned_publicly(): void
    {
        $this->clearGalleryData();

        GalleryRecord::query()->create([
            'slug' => 'draft-record',
            'type' => GalleryRecordType::CuratorNote->value,
            'title' => ['ko' => '초안 기록'],
            'body_sections' => [],
            'status' => GalleryPublicationStatus::Draft->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'relationship_state' => 'incomplete',
        ]);

        GalleryRecord::query()->create([
            'slug' => 'private-record',
            'type' => GalleryRecordType::CuratorNote->value,
            'title' => ['ko' => '비공개 기록'],
            'body_sections' => [],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Private->value,
            'published_at' => now(),
            'relationship_state' => 'incomplete',
        ]);

        GalleryRecord::query()->create([
            'slug' => 'public-record',
            'type' => GalleryRecordType::CuratorNote->value,
            'title' => ['ko' => '공개 기록'],
            'body_sections' => [],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'relationship_state' => 'complete',
        ]);

        $this->getJson('/api/gallery/records')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.slug', 'public-record');
    }

    public function test_response_keys_match_mock_replacement_contract_for_public_content(): void
    {
        $this->clearGalleryData();

        $exhibition = GalleryExhibition::query()->create([
            'slug' => 'public-exhibition',
            'title' => ['ko' => '공개 전시'],
            'summary' => ['ko' => '전시 요약'],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'sort_order' => 0,
        ]);

        GalleryItem::query()->create([
            'exhibition_id' => $exhibition->id,
            'slug' => 'public-item',
            'type' => GalleryItemType::Artwork->value,
            'title' => ['ko' => '공개 아이템'],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'sort_order' => 0,
            'relationship_state' => 'complete',
        ]);

        GalleryArchiveMaterial::query()->create([
            'slug' => 'public-material',
            'category' => GalleryArchiveCategory::Exhibition->value,
            'type' => GalleryArchiveMaterialType::Image->value,
            'title' => ['ko' => '공개 자료'],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'relationship_state' => 'complete',
        ]);

        $this->getJson('/api/gallery/exhibitions/public-exhibition')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'slug',
                    'title',
                    'title_label',
                    'subtitle',
                    'summary',
                    'period_label',
                    'location',
                    'featured_image',
                    'tags',
                    'metadata',
                    'sections',
                    'items',
                    'related_records',
                    'archive_materials',
                    'related_collections',
                    'timeline_references',
                    'href',
                ],
            ]);

        $this->getJson('/api/gallery/items/public-item')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'slug',
                    'type',
                    'title',
                    'artist_name',
                    'thumbnail_media',
                    'detail_media',
                    'visual',
                    'summary',
                    'exhibition_context',
                    'related_records',
                    'archive_materials',
                    'relationships',
                    'href',
                ],
            ]);

        $this->getJson('/api/gallery/archive/exhibition/public-material')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'slug',
                    'category',
                    'type',
                    'title',
                    'media',
                    'summary',
                    'description',
                    'related_exhibition',
                    'related_items',
                    'related_records',
                    'href',
                ],
            ]);
    }

    public function test_gallery_sample_seeder_is_idempotent_and_creates_relationship_graph(): void
    {
        $this->clearGalleryData();

        $seeder = new GallerySampleSeeder();
        $seeder->run();

        $firstCounts = $this->galleryCounts();

        $seeder->run();

        $this->assertSame($firstCounts, $this->galleryCounts());
        $this->assertSame(1, $firstCounts['gallery_exhibitions']);
        $this->assertSame(1, $firstCounts['gallery_exhibition_sections']);
        $this->assertSame(2, $firstCounts['gallery_items']);
        $this->assertSame(2, $firstCounts['gallery_records']);
        $this->assertSame(2, $firstCounts['gallery_archive_materials']);
        $this->assertSame(1, $firstCounts['gallery_collections']);
        $this->assertSame(4, $firstCounts['gallery_tags']);
        $this->assertSame(14, $firstCounts['gallery_relationships']);

        $this->assertEqualsCanonicalizing(
            GalleryRelationshipType::values(),
            GalleryRelationship::query()->distinct()->get(['type'])->map(fn (GalleryRelationship $relationship): string => $relationship->type->value)->all()
        );
    }

    public function test_seeded_public_content_is_served_by_gallery_api(): void
    {
        $this->clearGalleryData();

        (new GallerySampleSeeder())->run();

        $this->getJson('/api/gallery/exhibitions')
            ->assertOk()
            ->assertJsonPath('data.data.0.slug', 'light-afterimage-record')
            ->assertJsonPath('data.featured.slug', 'light-afterimage-record');

        $this->getJson('/api/gallery/exhibitions/light-afterimage-record')
            ->assertOk()
            ->assertJsonPath('data.slug', 'light-afterimage-record')
            ->assertJsonPath('data.items.0.slug', 'pale-grid-01')
            ->assertJsonCount(2, 'data.items')
            ->assertJsonCount(1, 'data.sections');

        $this->getJson('/api/gallery/items/pale-grid-01')
            ->assertOk()
            ->assertJsonPath('data.slug', 'pale-grid-01')
            ->assertJsonPath('data.exhibition_context.slug', 'light-afterimage-record')
            ->assertJsonStructure(['data' => ['relationships', 'related_records', 'archive_materials']]);

        $this->getJson('/api/gallery/records')
            ->assertOk()
            ->assertJsonCount(2, 'data.data')
            ->assertJsonPath('data.data.0.slug', 'field-note');

        $this->getJson('/api/gallery/records/curator-note')
            ->assertOk()
            ->assertJsonPath('data.slug', 'curator-note')
            ->assertJsonStructure(['data' => ['body_sections', 'related_items', 'archive_materials']]);

        $this->getJson('/api/gallery/archive')
            ->assertOk()
            ->assertJsonPath('data.total_materials', 2);

        $this->getJson('/api/gallery/archive/material')
            ->assertOk()
            ->assertJsonPath('data.materials.0.slug', 'installation-photo-set');

        $this->getJson('/api/gallery/archive/document/press-release')
            ->assertOk()
            ->assertJsonPath('data.slug', 'press-release')
            ->assertJsonStructure(['data' => ['description', 'related_items', 'related_records']]);
    }

    public function test_seeded_public_home_returns_curated_aggregate_payload(): void
    {
        $this->clearGalleryData();

        (new GallerySampleSeeder())->run();

        $response = $this->getJson('/api/gallery/home')
            ->assertOk()
            ->assertJsonPath('data.featured_exhibition.slug', 'light-afterimage-record')
            ->assertJsonCount(2, 'data.highlighted_items')
            ->assertJsonPath('data.highlighted_items.0.slug', 'pale-grid-01')
            ->assertJsonPath('data.highlighted_items.0.type_label_key', 'gallery.items.types.artwork')
            ->assertJsonCount(2, 'data.recent_records')
            ->assertJsonPath('data.recent_records.0.slug', 'field-note')
            ->assertJsonPath('data.recent_records.0.type_label_key', 'gallery.records.types.field_note')
            ->assertJsonCount(2, 'data.recent_archive_materials')
            ->assertJsonPath('data.recent_archive_materials.0.slug', 'press-release')
            ->assertJsonPath('data.recent_archive_materials.0.type_label_key', 'gallery.archive_materials.types.press')
            ->assertJsonCount(3, 'data.relationship_paths')
            ->assertJsonCount(6, 'data.archive_categories')
            ->assertJsonStructure([
                'data' => [
                    'featured_exhibition' => ['slug', 'title_label', 'href', 'published_at', 'published_at_display'],
                    'highlighted_items' => ['*' => ['slug', 'title_label', 'type', 'type_label_key', 'href']],
                    'recent_records' => ['*' => ['slug', 'title_label', 'type', 'type_label_key', 'recorded_at', 'recorded_at_display', 'href']],
                    'recent_archive_materials' => ['*' => ['slug', 'title_label', 'type', 'type_label_key', 'published_at', 'published_at_display', 'href']],
                    'relationship_paths' => ['*' => ['index', 'id', 'type', 'href', 'from_label', 'to_label']],
                    'archive_categories' => ['*' => ['slug', 'category', 'label_key', 'meta_key', 'body_key', 'materials_count', 'href']],
                    'about_context' => ['kicker_key', 'title_key', 'body_key', 'href'],
                ],
            ]);

        $payload = $response->json('data');

        $this->assertStringNotContainsString('T', $payload['featured_exhibition']['published_at_display']);
        $this->assertStringNotContainsString('T', $payload['recent_records'][0]['recorded_at_display']);
        $this->assertStringNotContainsString('T', $payload['recent_archive_materials'][0]['published_at_display']);
    }

    public function test_home_does_not_expose_draft_private_or_archived_content(): void
    {
        $this->clearGalleryData();

        $publicExhibition = GalleryExhibition::query()->create([
            'slug' => 'public-home-exhibition',
            'title' => ['ko' => '공개 홈 전시'],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'sort_order' => 0,
        ]);

        $hiddenExhibition = GalleryExhibition::query()->create([
            'slug' => 'hidden-home-exhibition',
            'title' => ['ko' => '숨겨진 홈 전시'],
            'status' => GalleryPublicationStatus::Archived->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'sort_order' => 0,
            'is_featured' => true,
        ]);

        GalleryItem::query()->create([
            'exhibition_id' => $publicExhibition->id,
            'slug' => 'public-home-item',
            'type' => GalleryItemType::Artwork->value,
            'title' => ['ko' => '공개 홈 항목'],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'sort_order' => 0,
            'relationship_state' => 'complete',
        ]);

        GalleryItem::query()->create([
            'exhibition_id' => $publicExhibition->id,
            'slug' => 'private-home-item',
            'type' => GalleryItemType::Artwork->value,
            'title' => ['ko' => '비공개 홈 항목'],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Private->value,
            'published_at' => now(),
            'sort_order' => 0,
            'relationship_state' => 'complete',
        ]);

        GalleryRecord::query()->create([
            'slug' => 'public-home-record',
            'type' => GalleryRecordType::CuratorNote->value,
            'title' => ['ko' => '공개 홈 기록'],
            'body_sections' => [],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'relationship_state' => 'complete',
        ]);

        GalleryRecord::query()->create([
            'slug' => 'draft-home-record',
            'type' => GalleryRecordType::CuratorNote->value,
            'title' => ['ko' => '초안 홈 기록'],
            'body_sections' => [],
            'status' => GalleryPublicationStatus::Draft->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'relationship_state' => 'complete',
        ]);

        GalleryArchiveMaterial::query()->create([
            'slug' => 'public-home-material',
            'category' => GalleryArchiveCategory::Document->value,
            'type' => GalleryArchiveMaterialType::Document->value,
            'title' => ['ko' => '공개 홈 자료'],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'relationship_state' => 'complete',
        ]);

        GalleryArchiveMaterial::query()->create([
            'slug' => 'archived-home-material',
            'category' => GalleryArchiveCategory::Document->value,
            'type' => GalleryArchiveMaterialType::Document->value,
            'title' => ['ko' => '보관 홈 자료'],
            'status' => GalleryPublicationStatus::Archived->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'relationship_state' => 'complete',
        ]);

        GalleryRelationship::query()->create([
            'type' => GalleryRelationshipType::ExhibitionItem->value,
            'source_type' => 'exhibition',
            'source_id' => $hiddenExhibition->id,
            'target_type' => 'item',
            'target_id' => 999999,
            'role' => 'hidden',
            'note' => ['ko' => '숨겨진 관계'],
            'sort_order' => 0,
        ]);

        $this->getJson('/api/gallery/home')
            ->assertOk()
            ->assertJsonPath('data.featured_exhibition.slug', 'public-home-exhibition')
            ->assertJsonMissing(['slug' => 'hidden-home-exhibition'])
            ->assertJsonMissing(['slug' => 'private-home-item'])
            ->assertJsonMissing(['slug' => 'draft-home-record'])
            ->assertJsonMissing(['slug' => 'archived-home-material'])
            ->assertJsonMissing(['from_label' => '숨겨진 홈 전시']);
    }

    public function test_seeded_api_keeps_draft_and_private_content_hidden(): void
    {
        $this->clearGalleryData();

        (new GallerySampleSeeder())->run();

        GalleryRecord::query()->create([
            'slug' => 'sample-draft-hidden',
            'type' => GalleryRecordType::ResearchNote->value,
            'title' => ['ko' => '숨겨진 초안'],
            'body_sections' => [],
            'status' => GalleryPublicationStatus::Draft->value,
            'visibility_state' => GalleryVisibilityState::Public->value,
            'published_at' => now(),
            'relationship_state' => 'incomplete',
        ]);

        GalleryRecord::query()->create([
            'slug' => 'sample-private-hidden',
            'type' => GalleryRecordType::ResearchNote->value,
            'title' => ['ko' => '숨겨진 비공개 기록'],
            'body_sections' => [],
            'status' => GalleryPublicationStatus::Published->value,
            'visibility_state' => GalleryVisibilityState::Private->value,
            'published_at' => now(),
            'relationship_state' => 'incomplete',
        ]);

        $this->getJson('/api/gallery/records')
            ->assertOk()
            ->assertJsonCount(2, 'data.data')
            ->assertJsonMissing(['slug' => 'sample-draft-hidden'])
            ->assertJsonMissing(['slug' => 'sample-private-hidden']);
    }

    private function galleryCounts(): array
    {
        return collect([
            'gallery_exhibitions',
            'gallery_exhibition_sections',
            'gallery_items',
            'gallery_records',
            'gallery_archive_materials',
            'gallery_collections',
            'gallery_tags',
            'gallery_taggables',
            'gallery_relationships',
        ])->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()])->all();
    }

    private function clearGalleryData(): void
    {
        foreach ([
            'gallery_relationships',
            'gallery_taggables',
            'gallery_tags',
            'gallery_archive_materials',
            'gallery_records',
            'gallery_items',
            'gallery_exhibition_sections',
            'gallery_collections',
            'gallery_exhibitions',
        ] as $table) {
            DB::table($table)->delete();
        }
    }
}
