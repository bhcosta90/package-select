<?php

declare(strict_types = 1);

namespace Brcas\Select;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class SelectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SelectTheme::class);
    }

    public function boot(): void
    {
        $published = resource_path('views/vendor/select/components');

        Blade::anonymousComponentPath(
            is_dir($published) ? $published : __DIR__ . '/../resources/views/components',
            'bhcosta90'
        );

        $this->publishes([
            __DIR__ . '/../config/select.php' => config_path('select.php'),
        ], 'select-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/select'),
        ], 'select-views');

    }
}
