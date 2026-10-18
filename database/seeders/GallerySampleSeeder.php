<?php

namespace Modules\Glitter\Gallery\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

class GallerySampleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();
            $publishedAt = now()->subDays(10);

            $exhibition = GalleryExhibition::query()->updateOrCreate(
                ['slug' => 'light-afterimage-record'],
                [
                    'title' => ['ko' => '빛이 지나간 자리의 기록', 'en' => 'Records of Where the Light Passed'],
                    'subtitle' => ['ko' => '전시와 현장 기록을 함께 읽는 현재 전시', 'en' => 'A current exhibition read through works and field records'],
                    'summary' => ['ko' => '작품, 설치 사진, 문서, 기록 노트를 하나의 조용한 열람 흐름으로 묶은 전시입니다.', 'en' => 'An exhibition that gathers works, installation images, documents, and record notes into one quiet reading path.'],
                    'period_label' => ['ko' => '2026.03.20 - 2026.05.31', 'en' => 'Mar 20 - May 31, 2026'],
                    'starts_at' => now()->subDays(51),
                    'ends_at' => now()->addDays(21),
                    'location' => ['ko' => 'Glitter Gallery Bz 메인 갤러리', 'en' => 'Glitter Gallery Bz Main Gallery'],
                    'curator_note' => ['ko' => '이 전시는 빛의 장면보다 빛이 지나간 뒤 남는 표면, 동선, 문서의 관계를 따라갑니다.', 'en' => 'This exhibition follows surfaces, movement, and documents left after light has passed.'],
                    'featured_image' => ['url' => '/images/gallery/featured-exhibition.jpg', 'alt' => ['ko' => '빛이 지나간 자리의 기록 전시 이미지', 'en' => 'Exhibition image for Records of Where the Light Passed'], 'credit' => 'Glitter Gallery Bz', 'attachment_id' => null],
                    'is_featured' => true,
                    'status' => GalleryPublicationStatus::Published->value,
                    'visibility_state' => GalleryVisibilityState::Public->value,
                    'published_at' => $publishedAt,
                    'sort_order' => 10,
                    'meta' => ['format' => ['ko' => '작품, 설치 기록, 큐레이터 노트', 'en' => 'Works, installation records, curator notes']],
                    'updated_at' => $now,
                ]
            );

            $section = GalleryExhibitionSection::query()->updateOrCreate(
                ['exhibition_id' => $exhibition->id, 'slug' => 'surface-and-field-records'],
                [
                    'title' => ['ko' => '표면과 현장 기록', 'en' => 'Surfaces and Field Records'],
                    'summary' => ['ko' => '작품 표면과 설치 현장의 판단을 함께 배치한 섹션입니다.', 'en' => 'A section pairing artwork surfaces with decisions made in the installation field.'],
                    'body' => ['ko' => '첫 번째 열람 흐름은 작품 앞에서 시작해 조명, 배치, 공개 문서로 이어집니다.', 'en' => 'The first reading path begins at the work and continues through lighting, placement, and public documents.'],
                    'insertion_point' => 'items',
                    'status' => GalleryPublicationStatus::Published->value,
                    'visibility_state' => GalleryVisibilityState::Public->value,
                    'published_at' => $publishedAt,
                    'sort_order' => 10,
                    'meta' => [],
                    'updated_at' => $now,
                ]
            );

            $items = [
                'pale-grid-01' => GalleryItem::query()->updateOrCreate(
                    ['slug' => 'pale-grid-01'],
                    [
                        'exhibition_id' => $exhibition->id,
                        'section_id' => $section->id,
                        'type' => GalleryItemType::Artwork->value,
                        'title' => ['ko' => '희미한 격자 01', 'en' => 'Pale Grid 01'],
                        'subtitle' => ['ko' => '빛의 잔상과 종이 표면을 기록한 작품', 'en' => 'A work recording afterimages of light and paper surface'],
                        'artist_name' => ['ko' => '민지원', 'en' => 'Min Jiwon'],
                        'year_label' => '2025',
                        'medium' => ['ko' => '한지에 안료, 아카이벌 프린트', 'en' => 'Pigment and archival print on hanji'],
                        'dimensions' => '72 x 54 cm',
                        'collection_name' => ['ko' => 'Glitter Gallery Bz 소장', 'en' => 'Glitter Gallery Bz Collection'],
                        'credit_line' => ['ko' => '작가 및 Glitter Gallery Bz 제공', 'en' => 'Courtesy of the artist and Glitter Gallery Bz'],
                        'archive_category' => GalleryArchiveCategory::Material->value,
                        'thumbnail_media' => ['url' => '/images/gallery/items/pale-grid-01-thumb.jpg', 'alt' => ['ko' => '희미한 격자 01 썸네일', 'en' => 'Pale Grid 01 thumbnail'], 'credit' => 'Glitter Gallery Bz', 'attachment_id' => null],
                        'detail_media' => ['url' => '/images/gallery/items/pale-grid-01.jpg', 'alt' => ['ko' => '희미한 격자 01 상세', 'en' => 'Pale Grid 01 detail'], 'credit' => 'Glitter Gallery Bz', 'attachment_id' => null],
                        'summary' => ['ko' => '전시의 첫 구간에서 빛이 사라진 뒤 남는 표면의 층을 보여주는 작품 기록입니다.', 'en' => 'A work record showing surface layers left after light disappears in the first exhibition section.'],
                        'curator_note' => ['ko' => '전시장 입구에서 관람자가 처음 마주하는 밝기와 종이의 밀도를 조절하는 기준점입니다.', 'en' => 'A reference point for the brightness and paper density visitors first encounter at the exhibition entrance.'],
                        'status' => GalleryPublicationStatus::Published->value,
                        'visibility_state' => GalleryVisibilityState::Public->value,
                        'published_at' => $publishedAt,
                        'sort_order' => 10,
                        'relationship_state' => 'complete',
                        'meta' => ['sample' => true],
                        'updated_at' => $now,
                    ]
                ),
                'installation-axis-record' => GalleryItem::query()->updateOrCreate(
                    ['slug' => 'installation-axis-record'],
                    [
                        'exhibition_id' => $exhibition->id,
                        'section_id' => $section->id,
                        'type' => GalleryItemType::Installation->value,
                        'title' => ['ko' => '설치 축 기록', 'en' => 'Installation Axis Record'],
                        'subtitle' => ['ko' => '작품 배치와 동선을 남긴 설치 자료', 'en' => 'Installation material preserving placement and movement'],
                        'artist_name' => ['ko' => '갤러리 기록팀', 'en' => 'Gallery Records Team'],
                        'year_label' => '2026',
                        'medium' => ['ko' => '설치 사진, 배치 메모, 조명 기록', 'en' => 'Installation photographs, placement notes, lighting records'],
                        'dimensions' => 'variable',
                        'collection_name' => ['ko' => 'Glitter Gallery Bz 아카이브', 'en' => 'Glitter Gallery Bz Archive'],
                        'credit_line' => ['ko' => 'Glitter Gallery Bz 기록팀', 'en' => 'Glitter Gallery Bz Records Team'],
                        'archive_category' => GalleryArchiveCategory::FieldRecord->value,
                        'thumbnail_media' => ['url' => '/images/gallery/items/installation-axis-record-thumb.jpg', 'alt' => ['ko' => '설치 축 기록 썸네일', 'en' => 'Installation Axis Record thumbnail'], 'credit' => 'Glitter Gallery Bz', 'attachment_id' => null],
                        'detail_media' => ['url' => '/images/gallery/items/installation-axis-record.jpg', 'alt' => ['ko' => '설치 축 기록 상세', 'en' => 'Installation Axis Record detail'], 'credit' => 'Glitter Gallery Bz', 'attachment_id' => null],
                        'summary' => ['ko' => '작품 사이의 거리와 시선 이동을 확인하기 위해 남긴 현장 기록입니다.', 'en' => 'A field record kept to review distance between works and the movement of sight.'],
                        'curator_note' => ['ko' => '작품 자체보다 전시가 만들어낸 이동과 간격을 보존하기 위한 설치 기록입니다.', 'en' => 'An installation record preserving movement and intervals produced by the exhibition.'],
                        'status' => GalleryPublicationStatus::Published->value,
                        'visibility_state' => GalleryVisibilityState::Public->value,
                        'published_at' => $publishedAt,
                        'sort_order' => 20,
                        'relationship_state' => 'complete',
                        'meta' => ['sample' => true],
                        'updated_at' => $now,
                    ]
                ),
            ];

            $records = [
                'curator-note' => GalleryRecord::query()->updateOrCreate(
                    ['slug' => 'curator-note'],
                    [
                        'type' => GalleryRecordType::CuratorNote->value,
                        'title' => ['ko' => '빛이 지나간 자리의 큐레이터 노트', 'en' => 'Curator Note for Records Where Light Passed'],
                        'subtitle' => ['ko' => '전시의 읽기 순서와 자료의 위치를 설명하는 장문 기록', 'en' => 'A long-form record explaining reading order and material positions'],
                        'author_name' => ['ko' => '수석 큐레이터', 'en' => 'Chief Curator'],
                        'recorded_at' => now()->subDays(9),
                        'summary' => ['ko' => '작품, 문서, 설치 기록을 하나의 전시 맥락 안에서 읽기 위해 작성한 큐레이터 노트입니다.', 'en' => 'A curator note written to read works, documents, and installation records inside one exhibition context.'],
                        'body_sections' => [['id' => 'opening', 'title' => ['ko' => '전시를 여는 기준', 'en' => 'Opening Frame'], 'body' => ['ko' => '관람은 작품 앞에서 끝나지 않고 문서와 설치 기록으로 천천히 이어집니다.', 'en' => 'Viewing continues slowly through documents and installation records.']]],
                        'primary_exhibition_id' => $exhibition->id,
                        'status' => GalleryPublicationStatus::Published->value,
                        'visibility_state' => GalleryVisibilityState::Public->value,
                        'published_at' => $publishedAt,
                        'relationship_state' => 'complete',
                        'meta' => ['sample' => true],
                        'updated_at' => $now,
                    ]
                ),
                'field-note' => GalleryRecord::query()->updateOrCreate(
                    ['slug' => 'field-note'],
                    [
                        'type' => GalleryRecordType::FieldNote->value,
                        'title' => ['ko' => '설치 주간 현장 노트', 'en' => 'Installation Week Field Note'],
                        'subtitle' => ['ko' => '공간 조건과 작품 배치를 기록한 현장 문서', 'en' => 'A field document recording space conditions and artwork placement'],
                        'author_name' => ['ko' => '현장 기록자', 'en' => 'Field Archivist'],
                        'recorded_at' => now()->subDays(8),
                        'summary' => ['ko' => '설치 과정에서 확인한 거리, 조명, 동선의 변화를 남긴 기록입니다.', 'en' => 'A record of distance, lighting, and circulation changes observed during installation.'],
                        'body_sections' => [['id' => 'field', 'title' => ['ko' => '조명과 거리', 'en' => 'Lighting and Distance'], 'body' => ['ko' => '작품 사이의 간격은 관람자의 속도와 표면 반응을 함께 조정합니다.', 'en' => 'Intervals between works tune visitor speed and surface response together.']]],
                        'primary_exhibition_id' => $exhibition->id,
                        'status' => GalleryPublicationStatus::Published->value,
                        'visibility_state' => GalleryVisibilityState::Public->value,
                        'published_at' => $publishedAt,
                        'relationship_state' => 'complete',
                        'meta' => ['sample' => true],
                        'updated_at' => $now,
                    ]
                ),
            ];

            $materials = [
                'installation-photo-set' => GalleryArchiveMaterial::query()->updateOrCreate(
                    ['category' => GalleryArchiveCategory::Material->value, 'slug' => 'installation-photo-set'],
                    [
                        'type' => GalleryArchiveMaterialType::InstallationPhoto->value,
                        'title' => ['ko' => '설치 사진 묶음', 'en' => 'Installation Photo Set'],
                        'subtitle' => ['ko' => '전시장 구조와 작품 배치를 확인하는 이미지 자료', 'en' => 'Image material showing exhibition structure and artwork placement'],
                        'date_label' => '2026.03 installation week',
                        'source' => ['ko' => 'Glitter Gallery Bz 기록팀', 'en' => 'Glitter Gallery Bz Records Team'],
                        'rights' => ['ko' => 'Glitter Gallery Bz 제공', 'en' => 'Courtesy of Glitter Gallery Bz'],
                        'format' => ['ko' => '디지털 이미지 묶음', 'en' => 'Digital image set'],
                        'identifier' => 'GBZ-MAT-2026-001',
                        'media' => ['url' => '/images/gallery/archive/installation-photo-set.jpg', 'alt' => ['ko' => '설치 사진 묶음', 'en' => 'Installation photo set'], 'credit' => 'Glitter Gallery Bz', 'attachment_id' => null],
                        'summary' => ['ko' => '전시 공간의 구성과 작품 사이의 관계를 확인할 수 있는 설치 사진 자료입니다.', 'en' => 'Installation photographs for reading exhibition structure and relationships between works.'],
                        'description' => ['ko' => '이 사진 묶음은 재설치와 연구 열람을 위한 기준 자료로 보존됩니다.', 'en' => 'This photo set is preserved as reference material for future reinstallation and research viewing.'],
                        'primary_exhibition_id' => $exhibition->id,
                        'status' => GalleryPublicationStatus::Published->value,
                        'visibility_state' => GalleryVisibilityState::Public->value,
                        'published_at' => $publishedAt,
                        'relationship_state' => 'complete',
                        'meta' => ['sample' => true],
                        'updated_at' => $now,
                    ]
                ),
                'press-release' => GalleryArchiveMaterial::query()->updateOrCreate(
                    ['category' => GalleryArchiveCategory::Document->value, 'slug' => 'press-release'],
                    [
                        'type' => GalleryArchiveMaterialType::Press->value,
                        'title' => ['ko' => '보도자료 원문', 'en' => 'Press Release Text'],
                        'subtitle' => ['ko' => '전시 공개 시점의 설명 문서', 'en' => 'Public explanation document from the exhibition opening'],
                        'date_label' => '2026.04.01',
                        'source' => ['ko' => '아카이브 에디터', 'en' => 'Archive Editor'],
                        'rights' => ['ko' => 'Glitter Gallery Bz 제공', 'en' => 'Courtesy of Glitter Gallery Bz'],
                        'format' => ['ko' => 'PDF 문서', 'en' => 'PDF document'],
                        'identifier' => 'GBZ-DOC-2026-001',
                        'media' => ['url' => '/images/gallery/archive/press-release.jpg', 'alt' => ['ko' => '보도자료 원문', 'en' => 'Press release text'], 'credit' => 'Glitter Gallery Bz', 'attachment_id' => null],
                        'summary' => ['ko' => '전시가 처음 외부에 설명된 방식을 확인할 수 있는 공개 문서입니다.', 'en' => 'A public document showing how the exhibition was first explained externally.'],
                        'description' => ['ko' => '보도자료는 이후 큐레이터 노트와 기록 문장 사이의 차이를 읽기 위한 기준점으로 보존됩니다.', 'en' => 'The press release remains a reference point for reading differences between curator notes and later archive sentences.'],
                        'primary_exhibition_id' => $exhibition->id,
                        'status' => GalleryPublicationStatus::Published->value,
                        'visibility_state' => GalleryVisibilityState::Public->value,
                        'published_at' => $publishedAt,
                        'relationship_state' => 'complete',
                        'meta' => ['sample' => true],
                        'updated_at' => $now,
                    ]
                ),
            ];

            $collection = GalleryCollection::query()->updateOrCreate(
                ['slug' => 'current-exhibition-records'],
                [
                    'title' => ['ko' => '현재 전시 기록 묶음', 'en' => 'Current Exhibition Record Set'],
                    'subtitle' => ['ko' => '작품과 현장 자료를 함께 묶은 컬렉션', 'en' => 'A collection pairing works and field materials'],
                    'summary' => ['ko' => '현재 전시의 작품, 기록 노트, 아카이브 자료로 이어지는 열람 묶음입니다.', 'en' => 'A reading set connecting works, record notes, and archive materials from the current exhibition.'],
                    'description' => ['ko' => '컬렉션은 전시 종료 이후에도 작품과 자료의 관계를 보존하기 위한 최소 단위입니다.', 'en' => 'The collection preserves relationships between works and materials after the exhibition closes.'],
                    'cover_media' => ['url' => '/images/gallery/collections/current-exhibition-records.jpg', 'alt' => ['ko' => '현재 전시 기록 묶음', 'en' => 'Current exhibition record set'], 'credit' => 'Glitter Gallery Bz', 'attachment_id' => null],
                    'status' => GalleryPublicationStatus::Published->value,
                    'visibility_state' => GalleryVisibilityState::Public->value,
                    'published_at' => $publishedAt,
                    'sort_order' => 10,
                    'meta' => ['sample' => true],
                    'updated_at' => $now,
                ]
            );

            $tags = [
                'light' => ['ko' => '빛', 'en' => 'Light'],
                'installation' => ['ko' => '설치', 'en' => 'Installation'],
                'field-record' => ['ko' => '현장 기록', 'en' => 'Field Record'],
                'research' => ['ko' => '연구', 'en' => 'Research'],
            ];

            $tagModels = [];
            $tagIndex = 10;
            foreach ($tags as $slug => $name) {
                $tagModels[$slug] = GalleryTag::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'description' => ['ko' => $name['ko'].' 관련 공개 태그', 'en' => $name['en'].' public tag'],
                        'taxonomy_group' => 'sample',
                        'sort_order' => $tagIndex,
                        'updated_at' => $now,
                    ]
                );
                $tagIndex += 10;
            }

            $exhibition->tags()->syncWithoutDetaching([$tagModels['light']->id, $tagModels['research']->id]);
            $items['pale-grid-01']->tags()->syncWithoutDetaching([$tagModels['light']->id, $tagModels['research']->id]);
            $items['installation-axis-record']->tags()->syncWithoutDetaching([$tagModels['installation']->id, $tagModels['field-record']->id]);
            $records['curator-note']->tags()->syncWithoutDetaching([$tagModels['research']->id]);
            $records['field-note']->tags()->syncWithoutDetaching([$tagModels['installation']->id, $tagModels['field-record']->id]);
            $materials['installation-photo-set']->tags()->syncWithoutDetaching([$tagModels['installation']->id, $tagModels['field-record']->id]);
            $materials['press-release']->tags()->syncWithoutDetaching([$tagModels['research']->id]);
            $collection->tags()->syncWithoutDetaching([$tagModels['light']->id, $tagModels['research']->id]);

            $this->relationship(GalleryRelationshipType::ExhibitionItem, 'exhibition', $exhibition->id, 'item', $items['pale-grid-01']->id, 'featured_item', 10);
            $this->relationship(GalleryRelationshipType::ExhibitionItem, 'exhibition', $exhibition->id, 'item', $items['installation-axis-record']->id, 'installation_item', 20);
            $this->relationship(GalleryRelationshipType::ItemRecord, 'item', $items['pale-grid-01']->id, 'record', $records['curator-note']->id, 'curatorial_context', 10);
            $this->relationship(GalleryRelationshipType::ItemRecord, 'item', $items['installation-axis-record']->id, 'record', $records['field-note']->id, 'field_context', 20);
            $this->relationship(GalleryRelationshipType::ItemArchiveMaterial, 'item', $items['pale-grid-01']->id, 'archive_material', $materials['installation-photo-set']->id, 'visual_reference', 10);
            $this->relationship(GalleryRelationshipType::ItemArchiveMaterial, 'item', $items['installation-axis-record']->id, 'archive_material', $materials['press-release']->id, 'document_reference', 20);
            $this->relationship(GalleryRelationshipType::RecordArchiveMaterial, 'record', $records['field-note']->id, 'archive_material', $materials['installation-photo-set']->id, 'field_evidence', 10);
            $this->relationship(GalleryRelationshipType::RecordArchiveMaterial, 'record', $records['curator-note']->id, 'archive_material', $materials['press-release']->id, 'publication_context', 20);
            $this->relationship(GalleryRelationshipType::CollectionItem, 'collection', $collection->id, 'item', $items['pale-grid-01']->id, 'collection_entry', 10);
            $this->relationship(GalleryRelationshipType::CollectionItem, 'collection', $collection->id, 'item', $items['installation-axis-record']->id, 'collection_entry', 20);
            $this->relationship(GalleryRelationshipType::ExhibitionRecord, 'exhibition', $exhibition->id, 'record', $records['curator-note']->id, 'main_note', 10);
            $this->relationship(GalleryRelationshipType::ExhibitionRecord, 'exhibition', $exhibition->id, 'record', $records['field-note']->id, 'field_note', 20);
            $this->relationship(GalleryRelationshipType::ExhibitionArchiveMaterial, 'exhibition', $exhibition->id, 'archive_material', $materials['installation-photo-set']->id, 'installation_material', 10);
            $this->relationship(GalleryRelationshipType::ExhibitionArchiveMaterial, 'exhibition', $exhibition->id, 'archive_material', $materials['press-release']->id, 'document_material', 20);
        });

        if ($this->command !== null) {
            $this->command->info('glitter-gallery sample seeder completed.');
        }
    }

    private function relationship(GalleryRelationshipType $type, string $sourceType, int $sourceId, string $targetType, int $targetId, string $role, int $sortOrder): void
    {
        GalleryRelationship::query()->updateOrCreate(
            [
                'type' => $type->value,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'target_type' => $targetType,
                'target_id' => $targetId,
            ],
            [
                'role' => $role,
                'note' => ['ko' => '샘플 관계 그래프의 공개 연결입니다.', 'en' => 'A public connection in the sample relationship graph.'],
                'confidence' => 'confirmed',
                'sort_order' => $sortOrder,
                'updated_at' => now(),
            ]
        );
    }
}
