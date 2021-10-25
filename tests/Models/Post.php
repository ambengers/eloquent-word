<?php

namespace Ambengers\EloquentWord\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Post extends Model implements HasMedia
{
    use HasMediaTrait;

    public function registerMediaCollections() : void
    {
        $this->addMediaCollection('attachments');
    }
}
