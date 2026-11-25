# Config

[![tests](https://github.com/jjgrainger/Config/actions/workflows/test.yml/badge.svg)](https://github.com/jjgrainger/Config/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/jjgrainger/Config/graph/badge.svg?token=JFOHSF8L1J)](https://codecov.io/gh/jjgrainger/Config)
[![Latest Stable Version](https://badgen.net/github/release/jjgrainger/Config/stable)](https://packagist.org/packages/jjgrainger/Config)
[![Total Downloads](https://badgen.net/packagist/dt/jjgrainger/Config)](https://packagist.org/packages/jjgrainger/Config)
[![License](https://badgen.net/github/license/jjgrainger/Config)](https://packagist.org/packages/jjgrainger/Config)

A lightweight PHP configuration library for loading configuration from files or directories.

Configuration is represented as an immutable object, with support for nested values and dot notation.

## Features

- Load configuration from a single file or directory.
- Support for PHP and JSON configuration files.
- Automatically discover configuration files in a directory.
- Group directory-based configuration by filename.
- Access nested configuration values using dot notation.
- Support custom loaders and parsers.

## Installation

Install via Composer:

```bash
composer require jjgrainger/config
```

## Basic usage

### Creating configuration

Load configuration from a directory:

```php
use Config\Config;

$config = Config::load(__DIR__ . '/config');
```

Load an individual file:

```php
$config = Config::load(__DIR__ . '/config/app.php');
```

Create configuration from an array.

```php
$config = new Config([
    'app' => [
        'name' => 'My Application',
    ],
]);
```

### Accessing values

Retrieve values using dot notation:

```php
$appName = $config->get('app.name');

$debug = $config->get('app.debug', false);
```

Check whether a key exists:

```php
if ($config->has('database.host')) {
    // ...
}
```

Retrieve the complete configuration array:

```php
$values = $config->all();
```

## Configuration files

When loading a directory, each supported file is loaded into a top-level namespace based on its filename.

For example:

```text
config/
├── app.php
├── database.json
└── services.php
```

Produces a configuration structure equivalent to:

```php
[
    'app' => [
        'name' => 'My Application',
        'debug' => false,
    ],
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
    ],
    'services' => [
        // ...
    ],
]
```

PHP configuration files must return an array:

```php
<?php

return [
    'name' => 'My Application',
    'debug' => false,
];
```

JSON configuration files must contain a JSON object or array.

```json
{
    "name": "My Application",
    "debug": false,
}
```

Directory discovery is non-recursive. Unsupported file extensions and duplicate configuration names result in exceptions rather than being silently ignored or overwritten.

## Custom parsers

The loader uses parser implementations to handle individual file formats. Custom parsers can be supplied when creating a loader:

```php
use Config\Config;
use Config\Loader;
use Config\Parsers\PhpParser;

$loader = new Loader([
    new PhpParser,
    new CustomParser,
]);

$config = Config::load(__DIR__ . '/config', $loader);
```

The default loader provides the built-in parsers.

Custom parsers must implement the `ParserContract`.

```php
<?php
use Config\Contracts\ParserContract;
use Config\Exceptions\InvalidConfigurationException;

class CustomParser implements ParserContract
{
    /**
     * Return true if file extension is supported.
     *
     * @param string $extension
     * @return boolean
     */
    public function supports(string $extension): bool
    {
        return 'php' === $extension;
    }

    /**
     * Parse configuration file.
     *
     * @param string $file
     * @return array<string, mixed>
     */
    public function parse(string $file): array
    {
        $config = require $file;

        if (!is_array($config)) {
            throw new InvalidConfigurationException(
                "Configuration file does not return an array: {$file}"
            );
        }

        return $config;
    }
}
```
