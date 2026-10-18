<?php

namespace Modules\Glitter\Gallery\Http\Controllers;

use App\Http\Controllers\Api\Base\PublicBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Glitter\Gallery\Services\PublicGalleryService;

class PublicExhibitionController extends PublicBaseController
{
    public function __construct(
        private PublicGalleryService $gallery,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        return $this->success('common.success', $this->gallery->exhibitions($request->only([
            'featured',
            'page',
            'per_page',
            'sort',
            'tag',
        ])));
    }

    public function show(string $slug): JsonResponse
    {
        $exhibition = $this->gallery->exhibition($slug);

        if (! $exhibition) {
            return $this->notFound('common.not_found');
        }

        return $this->success('common.success', $exhibition);
    }
}
