<?php

namespace Ambengers\EloquentWord;

use Ambengers\EloquentWord\Exceptions\DomainLogicException;
use Ambengers\EloquentWord\Exceptions\TemporaryFileMissedException;
use Spatie\MediaLibrary\FileAdder\FileAdder;
use Spatie\MediaLibrary\HasMedia\HasMedia;

trait InteractsWithMediaLibrary
{
    protected $fileAdder;

    protected $mediaCollection;

    /**
     * Set the mediaCollection property.
     *
     * @param  string $mediaCollection
     * @return $this
     */
    public function toMediaCollection(string $mediaCollection)
    {
        $this->mediaCollection = $mediaCollection;

        return $this;
    }

    /**
     * Process saving to media collection;.
     *
     * @return \Spatie\MediaLibrary\Models\Media
     *
     * @throws \Ambengers\EloquentPdf\Exceptions\DomainLogicException
     * @throws \Ambengers\EloquentPdf\Exceptions\TemporaryFileMissedException
     */
    public function saveToMediaCollection()
    {
        if (! $this->model instanceof HasMedia) {
            throw DomainLogicException::withMessage(
                class_basename($this->model).' must be an instance of Spatie\MediaLibrary\HasMedia\HasMedia.'
            );
        }

        // We want to save the document within the temporary directory first and let the
        // medialibrary package pick it up and transfer to the desired storage..
        $temporaryPath = $this->saveTemporaryFile();

        if (! file_exists($temporaryPath)) {
            throw TemporaryFileMissedException::withMessage(
                "File was not saved in temporary location: {$temporaryPath}"
            );
        }

        $media = $this->fileAdder->setSubject($this->model)
            ->setFile($temporaryPath)
            ->usingFileName($this->getFilenameWithExtension())
            ->toMediaCollection($this->mediaCollection);

        return $media;
    }

    protected function ensureFileAdderInstance()
    {
        if (! $this->fileAdder) {
            $this->fileAdder = app(FileAdder::class);
        }
    }
}
