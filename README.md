# Laravel Eloquent Word
This package provides an elegant way to generate Word documents with Eloquent Models.
Uses [PHPOffice/PHPWord](https://github.com/PHPOffice/PHPWord) package to generate Word documents and [Laravel Medialibrary](https://github.com/spatie/laravel-medialibrary) to associate Word documents as eloquent model media.

[![CircleCI](https://circleci.com/gh/ambengers/eloquent-word/tree/master.svg?style=svg)](https://circleci.com/gh/ambengers/eloquent-word/tree/master)
[![StyleCI](https://github.styleci.io/repos/419560299/shield?branch=master)](https://github.styleci.io/repos/419560299?branch=master)

## Medialibrary Compatibility

| Version  | Medialibrary |
|:---------|:-------------|
| v1.*     |~ 7.20        |
| v2.*     |^ 8.0         |
| v3.*     |^ 9.0         |

## Installation

Via Composer

``` bash
$ composer require ambengers/eloquent-word
```

Optionally, you can publish the config file by running the following command.
``` bash
php artisan vendor:publish --tag=eloquent-word-config
```

## Usage

### Eloquent Word class

You can generate your Eloquent Word class using the command
``` bash
$ php artisan make:eloquent-word PostWord
```
By default the class will be located at `App\Word` namespace. You can customize this in the config file.

Your Eloquent Word class will contain 2 methods:
 - `getData()` provides the data to be used on the view
 - `getView()` the name of the view file as word template

``` php
namespace App\Word;

use Ambengers\EloquentWord\AbstractEloquentWord;

class PostWord extends AbstractEloquentWord
{
    public function getData() : array
    {
        return [
            'title' => $this->model->title,
            'body'  => $this->model->body,
        ];
    }

    public function getView() : string
    {
        return 'posts.word';
    }
}
```

### View Template

Unlike PDF templates that uses html, Word templates are created using php scripts. So in your view template file, you can utilize the `@php` blade tags like so...

```php
@php

// You automatically have access to $word variable within your
// view template, which is an instance of PhpWord::class
$section = $word->addSection();

$section->addTitle($title);

$section->addTextBreak();

$section->addText($body);

@endphp
```
Within your view template, you automatically have access to `$word` variable which will give you an instance of `PhpOffice\PhpWord\PhpWord` class. This will allow you to get started formatting your Word document.

You can learn more by visiting the [PHPWord official documentation](https://phpword.readthedocs.io/en/latest/).

You can now use the Eloquent Word class from your controller (or anywhere in your application).

### Downloading Word

``` php
return app(PostWord::class)
    ->model($post)
    ->handle();
```

### Eloquent Word with Medialibrary

This package also offers an elegant way to associate Word document to the Eloquent Model using Medialibrary package.
To do that, you will need to use a trait on your Eloquent Word class.

``` php
use Ambengers\EloquentWord\InteractsWithMediaLibrary;

class PostWord extends AbstractEloquentWord
{
    use InteractsWithMediaLibrary;
}
```

Then on your controller, much like how you would do with medialibrary, just provide the collection name in which the Word document will be associated with.

``` php
return app(PostWord::class)
    ->model($post)
    ->toMediaCollection('reports')
    ->handle();
```

For additional convenience you can also chain other medialibrary methods.

``` php
return app(PostWord::class)
    ->model($post)
    ->toMediaCollection('reports')
    ->withCustomProperties(['foo' => 'bar'])
    ->withAttributes(['creator_id' => auth()->id()])
    ->handle();
```

Behind the scenes, Eloquent Word will forward these method calls to the medialibrary `FileAdder::class` so you can further take advantage of its features.

### Customizations

If you need to customize the default filename or extension, you can chain some setter methods when you call your Eloquent Word class.

``` php
return app(PostWord::class)
    ->model($post)
    ->filename('some-cool-filename')
    ->extension('odt')
    ->toMediaCollection('reports')
    ->handle();
```

## Security

If you discover any security related issues, please send the author an email instead of using the issue tracker.

## License

Please see the [license file](license.md) for more information.
