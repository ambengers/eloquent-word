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