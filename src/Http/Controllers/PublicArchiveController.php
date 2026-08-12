<?php

namespace Modules\Glitter\Gallery\Http\Controllers;

use App\Http\Controllers\Api\Base\PublicBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Glitter\Gallery\Services\PublicGalleryService;

class PublicArchiveController extends PublicBaseController
{
    public function __construct(
        private PublicGalleryService $gallery,
    ) {
        parent::__construct();
    }

    public function index(): JsonResponse
    {
        return $this->success('common.success', $this->gallery->archive());
    }

    public function category(Request $request, string $category): JsonResponse
    {
        $payload = $this->gallery->archiveCategory($category, $request->only([
            'type',
            'tag',
            'exhibition',
            'source',
            'page',
            'per_page',
            'sort',
        ]));

        if (! $payload) {
            return $this->notFound('common.not_found');
        }

        return $this->success('common.success', $payload);
    }

    public function show(string $category, string $slug): JsonResponse
    {
        $material = $this->gallery->archiveMaterial($category, $slug);

        if (! $material) {
            return $this->notFound('common.not_found');
        }

        return $this->success('common.success', $material);
    }
}
