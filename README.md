# handlebars.php
Ultra fast PHP8 implementation of handlebars.js

[![Build Status](https://travis-ci.com/phpugph/handlebars.svg?branch=main)](https://travis-ci.com/phpugph/handlebars)
[![Coverage Status](https://coveralls.io/repos/github/phpugph/handlebars/badge.svg?branch=main)](https://coveralls.io/github/phpugph/handlebars?branch=main)
[![Latest Stable Version](https://poser.pugx.org/phpugph/handlebars/v/stable)](https://packagist.org/packages/phpugph/handlebars)
[![Total Downloads](https://poser.pugx.org/phpugph/handlebars/downloads)](https://packagist.org/packages/phpugph/handlebars)
[![Latest Unstable Version](https://poser.pugx.org/phpugph/handlebars/v/unstable)](https://packagist.org/packages/phpugph/handlebars)
[![License](https://poser.pugx.org/phpugph/handlebars/license)](https://packagist.org/packages/phpugph/handlebars)

## Install

```bash
$ composer install phpugph/handlebars
```

<a name="intro"></a>
## Introduction

PHP Handlebars and JS interface to match with compile time helper support and super nice compile
time error reporting. This version of Handlebars is based on caching the compiled templates and
inherently made the overall compile times faster. Loading at ~50ms uncached and ~30ms cached.

<a name="usage"></a>
## Basic Usage

#### Rendering

```php
use Handlebars\HandlebarsHandler as Handlebars;
$handlebars = new Handlebars();
$template = $handlebars->compile('{{foo}} {{bar}}');

echo $template(['foo' => 'BAR', 'bar' => 'ZOO']);
```

#### Registering Helpers

```
$handlebars->registerHelper('bar', function($options) {
    return 'ZOO';
});

$template = $handlebars->compile('{{foo}} {{bar}}');

echo $template(['foo' => 'BAR']);
```

#### Registering Partials

```
$handlebars->registerPartial('bar', 'zoo');
$template = $handlebars->compile('{{foo}} {{> bar}}');

echo $template(['foo' => 'BAR']);
```

----

<a name="features"></a>
## Features

 - PHP API - designed to match the handlebars.js documentation
    - registerHelper() - Matches exactly what you expect from handlebars.js (except it's PHP syntax)
    - registerPartial() - accepts strings and functions as callbacks
    - Literals like `{{./foo}}` and `{{../bar}}` are evaluated properly
    - Comments like `{{!-- Something --}}` and `{{! Something }}` supported
    - Trims like `{{~#each}}` and `{{~foo~}}` supported
    - Mustache backwards compatibility `{{#foo}}{{this}}{{/foo}}`
    - Tokenizer helpers to optimize custom code generation to cache
    - Event handlers for unknown helpers and unknown partials
 - Default Helpers matching handlebars.js
     - each - and `{{#each foo as |value, key|}}`
         - Please note that there is an issue with `each` being slow depending on the size of the object
         - We need help optimizing this
     - with
     - unless
     - if

<a name="defeatures"></a>
## De-Features (or whatever the opposite of features is)

 - Does not support file templates.
     - You need to load them up and pass it into Handlebars.
     - If this is a problem you should consider other Handlebars PHP libraries
     - You can always create a helper for this
     - This de-feature will be considered upon requests ( create an issue :) )
 - Partial Failover
     - Something we haven't had a chance to come around doing yet as we did not have a need
     - This de-feature will be considered upon requests ( create an issue :) )
 - Safe String/Escaping
     - PHP has functions that can turn a string "safe".
     - We didn't want to create something that already exists in other contexts
     - This de-feature will be considered upon requests ( create an issue :) )
 - Utils
     - PHP has functions that support most of the listed Utils in handlebars.js
     - We didn't want to create something that already exists in other contexts
     - This de-feature will be considered upon requests ( create an issue :) )
 - Dynamic Partials
     - At the bottom of our pipe
     - because of it's difficulty to recreate
     - and practicality
     - This de-feature will be considered upon requests ( create an issue :( )
 - Inline Partials
     - TODO
 - Decorators
     - TODO
 - Frames
     - TODO

----

<a name="production"></a>
## Production Ready

When your templates are ready for a production (live) environment, it is recommended that caching be used. To enable cache:

 - Create a cache folder and make sure permissions are properly set for handlebars to write files to it.
 - Enable cache by using `$handlebars->setCache(__DIR__.'/your/cache/folder/location');`
 - If the folder location does not exist, caching will be disabled.

----

<a name="api"></a>
## API

<a name="compile"></a>

### compile

Returns a callback that binds the data with the template

#### Usage

```
$handlebars->compile(string $string);
```

#### Parameters

 - `string $string` - the template string

Returns `function` - the template binding handler

#### Example

```
$handlebars->compile();
```

----

<a name="getCache"></a>

### getCache

Returns the active cache path

#### Usage

```
$handlebars->getCache();
```

Returns `Closure`

----

<a name="getHelper"></a>

### getHelper

Returns a helper given the name

#### Usage

```
$handlebars->getHelper('if');
```

#### Parameters

- `string $name` - the name of the helper

Returns `Closure`

----

<a name="getHelpers"></a>

### getHelpers

Returns all the registered helpers

#### Usage

```
$handlebars->getHelpers();
```

#### Parameters

Returns `array`

----

<a name="getPartial"></a>

### getPartial

Returns a partial given the name

#### Usage

```
$handlebars->getPartial('foobar');
```

#### Parameters

- `string $name` - the name of the partial

Returns `string`

----

<a name="getPartials"></a>

### getPartials

Returns all the registered partials

#### Usage

```
$handlebars->getPartials();
```

#### Parameters

Returns `array`

----

<a name="registerHelper"></a>

### registerHelper

The famous register helper matching the Handlebars API

#### Usage

```
$handlebars->registerHelper(string $name, function $helper);
```

#### Parameters

 - `string $name` - the name of the helper
 - `function $helper` - the helper handler

Returns `Handlebrs\Index`

#### Example

```
$handlebars->registerHelper();
```

----

<a name="registerPartial"></a>

### registerPartial

Delays registering partials to the engine because there is no add partial method...

#### Usage

```
$handlebars->registerPartial(string $name, string $partial);
```

#### Parameters

 - `string $name` - the name of the helper
 - `string $partial` - the helper handler

Returns `Handlebrs\Index`

#### Example

```
$handlebars->registerPartial();
```

----

<a name="setCache"></a>

### setCache

Enables the cache option

#### Usage

```
$handlebars->setCache(string $path);
```

#### Parameters

 - `string $path` - The cache path

Returns `Handlebrs\Index`

#### Example

```
$handlebars->setCache('/path/to/cache/folder');
```

----

<a name="setPrefix"></a>

### setPrefix

Sets the file name prefix for caching

#### Usage

```
$handlebars->setPrefix(string $prefix);
```

#### Parameters

 - `string $prefix` - Custom prefix name

Returns `Handlebrs\Index`

#### Example

```
$handlebars->setPrefix('special-template-');
```

----

<a name="unregisterHelper"></a>

### unregisterHelper

The opposite of registerHelper

#### Usage

```
$handlebars->unregisterHelper(string $name);
```

#### Parameters

 - `string $name` - the helper name

Returns `Handlebars\Index`

#### Example

```
$handlebars->unregisterHelper();
```

----

<a name="unregisterPartial"></a>

### unregisterPartial

The opposite of registerPartial

#### Usage

```
$handlebars->unregisterPartial(string $name);
```

#### Parameters

 - `string $name` - the partial name

Returns `Handlebars\Index`

#### Example

```
$handlebars->unregisterPartial();
```
