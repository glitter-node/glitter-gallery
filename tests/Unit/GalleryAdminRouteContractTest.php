<?php

namespace Modules\Glitter\Gallery\Tests\Unit;

use PHPUnit\Framework\TestCase;

class GalleryAdminRouteContractTest extends TestCase
{
    public function test_gallery_dashboard_is_declared_as_a_typed_admin_module_route(): void
    {
        $routesPath = dirname(__DIR__, 2).'/resources/routes/admin.json';

        $this->assertFileExists($routesPath);

        $routes = json_decode((string) file_get_contents($routesPath), true, flags: JSON_THROW_ON_ERROR);
        $dashboard = collect($routes['routes'] ?? [])->firstWhere('path', '*/admin/gallery');

        $this->assertIsArray($dashboard);
        $this->assertSame('admin_gallery_dashboard', $dashboard['layout'] ?? null);
        $this->assertTrue($dashboard['auth_required'] ?? false);
        $this->assertSame('$t:glitter-gallery.admin.dashboard.title', $dashboard['meta']['title'] ?? null);
    }

    public function test_gallery_dashboard_layout_and_translations_follow_the_module_contract(): void
    {
        $layoutPath = dirname(__DIR__, 2).'/resources/layouts/admin/admin_gallery_dashboard.json';

        $this->assertFileExists($layoutPath);

        $layout = json_decode((string) file_get_contents($layoutPath), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('admin_gallery_dashboard', $layout['layout_name'] ?? null);
        $this->assertSame('_admin_base', $layout['extends'] ?? null);
        $this->assertSame('$t:glitter-gallery.admin.dashboard.title', $layout['meta']['title'] ?? null);

        foreach (['ko', 'en'] as $locale) {
            $langPath = dirname(__DIR__, 2)."/resources/lang/{$locale}.json";
            $this->assertFileExists($langPath);

            $translations = json_decode((string) file_get_contents($langPath), true, flags: JSON_THROW_ON_ERROR);
            $this->assertIsString($translations['admin']['dashboard']['title'] ?? null);
            $this->assertNotSame('', $translations['admin']['dashboard']['title'] ?? '');
        }
    }
}
