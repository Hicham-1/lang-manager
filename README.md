# Laravel Language Manager

A Laravel package for create language keys on your lang file from a given path (like views/).

## Installation

You can install the package via Composer:

```bash
composer require h1ch4m/lang-manager
```
## Service Provider
After installing the package, you need to register the service provider. Add the service provider to your `config/app.php` file:

```php
'providers' => [
    // Other Service Providers...

    H1ch4m\LangManager\LangManagerServiceProvider::class,
],
```

## Usage

Your sentence should be inside the function ``__()`` like:

```html
<h5>{{ __('Your sentence here') }}</h5>
```

``⚠️ Please commit your changes before run the command, just to be able to reset your work if you don't like the command result.``
<br>
<br>
To run the command:

```bash
php artisan lang-manager:start
```
It will ask you
```bash
Enter your directory (default is resources/views):
```
if your directory is ``resources/views`` click enter, other ways enter your path that you want to create keys for it.
Than it will ask you for the language:
```bash
Enter your files language (default is en):
```
If your default language is en than click enter, other ways enter your language and hit enter.
<br>
You will notice that new files will be created on the lang folder and the sentence on ``__()`` will be changed to the keys on the lang files.