<?php

namespace Modules\Glitter\Gallery\Http\Controllers;

use App\Http\Controllers\Api\Base\PublicBaseController;
use Illuminate\Http\JsonResponse;
use Modules\Glitter\Gallery\Services\PublicGalleryService;

class PublicHomeController extends PublicBaseController
{
    public function __construct(
        private PublicGalleryService $gallery,
    ) {
        parent::__construct();
    }

    public function __invoke(): JsonResponse
    {
        return $this->success('common.success', $this->gallery->home());
    }
}
