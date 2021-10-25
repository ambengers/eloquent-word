<?php

namespace Ambengers\EloquentWord\Tests\Features;

use Ambengers\EloquentWord\AbstractEloquentWord;
use Ambengers\EloquentWord\InteractsWithMediaLibrary;
use Ambengers\EloquentWord\Tests\BaseTestCase;
use Ambengers\EloquentWord\Tests\Models\Post;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EloquentWordTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        $this->post = (new Post)->fill([
            'title' => 'Test title',
            'body' => 'Test body'
        ]);
    }

    public function tearDown() : void
    {
        $filesystem = new Filesystem;

        $filesystem->cleanDirectory(storage_path('app'));
        $filesystem->cleanDirectory(storage_path('temp'));

        parent::tearDown();
    }

    /** @test */
    public function it_downloads_Word()
    {
        $word = app(PostWord::class)
            ->model($this->post)
            ->filename(time());

        $response = $word->handle();

        $this->assertEquals(
            'attachment; filename="'.$word->getFilenameWithExtension().'"',
            $response->headers->all()['content-disposition'][0]
        );
    }

    /** @test */
    public function it_transfers_to_medialibrary()
    {
        $this->post->save();

        $word = app(PostWord::class)
            ->model($this->post)
            ->filename(time())
            ->toMediaCollection($collectionName = 'attachments');

        $word->handle();

        $this->assertDatabaseHas('media', [
            'model_type' => $this->post->getMorphClass(),
            'model_id' => $this->post->getKey(),
            'collection_name' => $collectionName,
            'name' => $word->getFilename(),
            'file_name' => $word->getFilenameWithExtension(),
            'disk' => config('filesystems.default'),
            'mime_type' => 'application/octet-stream',
        ]);
    }

    /** @test */
    public function it_can_include_additional_attributes_when_transferring_to_medialibrary()
    {
        $this->post->save();

        $word = app(PostWord::class)
            ->model($this->post)
            ->filename(time())
            ->withAttributes(['custom_properties' => $custom = ['attribute' => 'value']])
            ->toMediaCollection($collectionName = 'attachments');

        $word->handle();

        $this->assertDatabaseHas('media', [
            'model_type' => $this->post->getMorphClass(),
            'model_id' => $this->post->getKey(),
            'collection_name' => $collectionName,
            'name' => $word->getFilename(),
            'custom_properties' => json_encode($custom),
            'file_name' => $word->getFilenameWithExtension(),
            'disk' => config('filesystems.default'),
            'mime_type' => 'application/octet-stream',
        ]);
    }


    /** @test */
    public function it_can_include_custom_properties_when_transferring_to_medialibrary()
    {
        $this->post->save();

        $word = app(PostWord::class)
            ->model($this->post)
            ->filename(time())
            ->withCustomProperties($props = ['foo' => 1, 'bar' => 2])
            ->toMediaCollection($collectionName = 'attachments');

        $word->handle();

        $this->assertDatabaseHas('media', [
            'model_type' => $this->post->getMorphClass(),
            'model_id' => $this->post->getKey(),
            'collection_name' => $collectionName,
            'name' => $word->getFilename(),
            'file_name' => $word->getFilenameWithExtension(),
            'disk' => config('filesystems.default'),
            'mime_type' => 'application/octet-stream',
            'custom_properties' => json_encode($props),
        ]);
    }
}

class PostWord extends AbstractEloquentWord
{
    use InteractsWithMediaLibrary;

    /**
     * The name of the view file for the Word
     *
     * @return string
     */
    public function getView() : string
    {
        return 'test::post-word';
    }

    /**
     * Array of data to be used on the view.
     *
     * @return array
     */
    public function getData() : array
    {
        return [
            'title' => $this->model->title,
            'body' => $this->model->body,
        ];
    }
}
