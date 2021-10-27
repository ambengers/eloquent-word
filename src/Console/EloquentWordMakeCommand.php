<?php

namespace Ambengers\EloquentWord\Console;

use Illuminate\Console\GeneratorCommand;

class EloquentWordMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:eloquent-word
        {name : The name of the Eloquent Word class}
        {--view= : Create a new view template on the specified location}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent Word class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'EloquentWord';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() === false) {
            return;
        }

        $this->writeViewTemplate();
    }

    /**
     * Write the view template.
     *
     * @return void
     */
    protected function writeViewTemplate()
    {
        $path = $this->viewPath(
            str_replace('.', '/', $this->option('view')).'.blade.php'
        );

        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }

        $this->files->put($path, file_get_contents(__DIR__.'/stubs/view.stub'));
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $class = parent::buildClass($name);

        $class = str_replace('DummyView', $this->option('view') ?? '', $class);

        return $class;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/class.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('eloquent_word.namespace');
    }
}
