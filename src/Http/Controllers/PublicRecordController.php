<?php

namespace Modules\Glitter\Gallery\Http\Controllers;

use App\Http\Controllers\Api\Base\PublicBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Glitter\Gallery\Services\PublicGalleryService;

class PublicRecordController extends PublicBaseController
{
    public function __construct(
        private PublicGalleryService $gallery,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        return $this->success('common.success', $this->gallery->records($request->only([
            'type',
            'tag',
            'exhibition',
            'author',
            'page',
            'per_page',
            'sort',
        ])));
    }

    public function show(string $slug): JsonResponse
    {
        $record = $this->gallery->record($slug);

        if (! $record) {
            return $this->notFound('common.not_found');
        }

        return $this->success('common.success', $record);
    }
}
