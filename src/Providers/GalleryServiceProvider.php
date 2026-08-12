<?php

namespace Modules\Glitter\Gallery\Providers;

use App\Enums\ExtensionStatus;
use App\Extension\BaseModuleServiceProvider;
use App\Models\Module;
use Illuminate\Support\Facades\Route;
use Modules\Glitter\Gallery\Repositories\ArchiveMaterialRepository;
use Modules\Glitter\Gallery\Repositories\Contracts\ArchiveMaterialRepositoryInterface;
use Modules\Glitter\Gallery\Repositories\Contracts\ExhibitionRepositoryInterface;
use Modules\Glitter\Gallery\Repositories\Contracts\ItemRepositoryInterface;
use Modules\Glitter\Gallery\Repositories\Contracts\RecordRepositoryInterface;
use Modules\Glitter\Gallery\Repositories\ExhibitionRepository;
use Modules\Glitter\Gallery\Repositories\ItemRepository;
use Modules\Glitter\Gallery\Repositories\RecordRepository;

class GalleryServiceProvider extends BaseModuleServiceProvider
{
    protected string $moduleIdentifier = 'glitter-gallery';

    protected array $repositories = [
        ExhibitionRepositoryInterface::class => ExhibitionRepository::class,
        ItemRepositoryInterface::class => ItemRepository::class,
        RecordRepositoryInterface::class => RecordRepository::class,
        ArchiveMaterialRepositoryInterface::class => ArchiveMaterialRepository::class,
    ];

    public function boot(): void
    {
        parent::boot();

        if ($this->shouldLoadPublicRoutes()) {
            Route::middleware('api')
                ->prefix('api/gallery')
                ->name('api.gallery.')
                ->group(dirname(__DIR__).'/routes/public.php');
        }
    }

    private function shouldLoadPublicRoutes(): bool
    {
        if ($this->app->environment('testing')) {
            return true;
        }

        return Module::query()
            ->where('identifier', $this->moduleIdentifier)
            ->where('status', ExtensionStatus::Active->value)
            ->exists();
    }
}
