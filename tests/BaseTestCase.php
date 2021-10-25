<?php

namespace Ambengers\EloquentWord\Tests;

use Ambengers\EloquentWord\EloquentWordServiceProvider;
use Orchestra\Testbench\TestCase;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

abstract class BaseTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            MediaLibraryServiceProvider::class,
            EloquentWordServiceProvider::class,
            TestServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->useStoragePath(__DIR__.'/storage');

        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('filesystems.default', 'public');
        $app['config']->set('filesystems.disks.public.root', __DIR__.'/storage/app/public');

        $app['config']->set('medialibrary.max_file_size', 1024 * 1024 * 1000);
        $app['config']->set('medialibrary.media_model', \Spatie\MediaLibrary\Models\Media::class);
    }

    protected function setUp() : void
    {
        parent::setUp();

        $this->loadMigrationsFrom([
            '--database' => 'sqlite',
            '--path' => realpath(__DIR__.'/Migrations'),
        ]);
    }
}
