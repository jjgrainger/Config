<?php

namespace Config\Parsers;

use Config\Contracts\ParserContract;
use Config\Exceptions\InvalidConfigurationException;

class JsonParser implements ParserContract
{
    public function supports(string $extension): bool
    {
        return 'json' === $extension;
    }

    public function parse(string $file): array
    {
        $contents = file_get_contents($file);

        if (false === $contents) {
            throw new InvalidConfigurationException(
                "File contents cannot be read: {$file}"
            );
        }

        $config = json_decode(
            $contents,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!is_array($config)) {
            throw new InvalidConfigurationException(
                "Configuration file does not return an array: {$file}"
            );
        }

        return $config;
    }
}
