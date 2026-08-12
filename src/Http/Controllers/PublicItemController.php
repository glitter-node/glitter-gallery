<?php

namespace Modules\Glitter\Gallery\Http\Controllers;

use App\Http\Controllers\Api\Base\PublicBaseController;
use Illuminate\Http\JsonResponse;
use Modules\Glitter\Gallery\Services\PublicGalleryService;

class PublicItemController extends PublicBaseController
{
    public function __construct(
        private PublicGalleryService $gallery,
    ) {
        parent::__construct();
    }

    public function show(string $slug): JsonResponse
    {
        $item = $this->gallery->item($slug);

        if (! $item) {
            return $this->notFound('common.not_found');
        }

        return $this->success('common.success', $item);
    }
}
