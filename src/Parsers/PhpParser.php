<?php

namespace Config\Parsers;

use Config\Contracts\ParserContract;
use Config\Exceptions\InvalidConfigurationException;

class PhpParser implements ParserContract
{
    public function supports(string $extension): bool
    {
        return 'php' === $extension;
    }

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
