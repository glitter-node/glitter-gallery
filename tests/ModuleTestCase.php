<?php

namespace Modules\Glitter\Gallery\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Modules\Glitter\Gallery\Http\Controllers\PublicHomeController;
use Modules\Glitter\Gallery\Providers\GalleryServiceProvider;
use Tests\TestCase;

abstract class ModuleTestCase extends TestCase
{
    use DatabaseTransactions;

    protected static bool $migrated = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(GalleryServiceProvider::class);
        $this->registerPublicRoutes();
        $this->runModuleMigrationIfNeeded();
    }

    protected function registerPublicRoutes(): void
    {
        if (Route::getRoutes()->getByName('api.gallery.exhibitions.index')) {
            if (! Route::getRoutes()->getByName('api.gallery.home')) {
                Route::middleware('api')
                    ->prefix('api/gallery')
                    ->name('api.gallery.')
                    ->get('home', PublicHomeController::class)
                    ->middleware(['optional.sanctum', 'throttle:600,1']);
            }

            return;
        }

        Route::middleware('api')
            ->prefix('api/gallery')
            ->name('api.gallery.')
            ->group(dirname(__DIR__).'/src/routes/public.php');
    }

    protected function runModuleMigrationIfNeeded(): void
    {
        if (static::$migrated) {
            return;
        }

        if (! Schema::hasTable('users') || ! Schema::hasTable('modules')) {
            $this->artisan('migrate');
        }

        if (! Schema::hasTable('gallery_exhibitions')) {
            $this->artisan('migrate', [
                '--path' => dirname(__DIR__).'/database/migrations',
                '--realpath' => true,
            ]);
        }

        static::$migrated = true;
    }
}
