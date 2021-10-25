<?php

namespace Ambengers\EloquentWord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Traits\ForwardsCalls;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;

abstract class AbstractEloquentWord
{
    use ForwardsCalls;

    protected $word;

    protected $filename = 'document';

    protected $extension = 'docx';

    protected $model;

    protected $mediaCollection;

    public function __construct(PhpWord $phpWord)
    {
        $this->word = $phpWord;

        app()->instance('word', $this->word);

        if ($this->isInteractingWithMediaLibrary()) {
            $this->ensureFileAdderInstance();
        }

        $this->loadSettings();
    }

    public function handle()
    {
        $this->renderView();

        if ($this->isInteractingWithMediaLibrary() && $this->mediaCollection) {
            return $this->saveToMediaCollection();
        }

        $temporaryPath = $this->saveTemporaryFile();

        return new Response(file_get_contents($temporaryPath), 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$this->getFilenameWithExtension().'"'
        ]);
    }

    public function model(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    public function filename($filename = 'word_document')
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Set the document extension.
     *
     * @param  string $extension
     * @return string
     */
    public function extension($extension = 'docx') : string
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get the document filename.
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Get the document filename with extension.
     *
     * @return string
     */
    public function getFilenameWithExtension() : string
    {
        return $this->filename . '.' . $this->extension;
    }

    protected function loadSettings()
    {
        Settings::setOutputEscapingEnabled(true);
    }

    abstract public function getView() : string;

    abstract public function getData() : array;

    /**
     * Determine if class is interacting with media library.
     *
     * @return bool
     */
    public function isInteractingWithMediaLibrary()
    {
        return in_array(InteractsWithMediaLibrary::class, class_uses($this));
    }

    /**
     * Dynamically handle method calls.
     *
     * @param  string $method
     * @param  array $parameter
     * @return self
     */
    public function __call($method, $parameter)
    {
        if ($this->isInteractingWithMediaLibrary()) {
            $this->forwardCallTo($this->fileAdder, $method, $parameter);
        }

        return $this;
    }

    protected function renderView()
    {
        $data = array_merge($this->getData(), ['word' => $this->word]);

        return view($this->getView(), $data)->render();
    }

    protected function getTemporaryPath($filename)
    {
        return $this->getTemporaryDirectory().'/'.$filename;
    }

    protected function getTemporaryDirectory()
    {
        return config('eloquent_word.temporary_directory') ?? storage_path('temp/word');
    }

    protected function saveTemporaryFile()
    {
        $this->ensureTemporaryDirectoryExists();

        $path = $this->getTemporaryPath($this->getFilenameWithExtension());

        IOFactory::createWriter($this->word, 'Word2007')->save($path);

        return $path;
    }

    protected function ensureTemporaryDirectoryExists()
    {
        if (! file_exists($this->getTemporaryDirectory()) || ! is_dir($this->getTemporaryDirectory())) {
            mkdir($this->getTemporaryDirectory(), 0755, true);
        }
    }
}
