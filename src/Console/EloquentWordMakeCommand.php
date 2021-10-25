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
    protected $signature = 'make:eloquent-word {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent Word class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'EloquentWord';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/EloquentWord.stub';
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
