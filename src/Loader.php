<?php

namespace Config;

use Config\Contracts\LoaderContract;
use Config\Contracts\ParserContract;
use Config\Contracts\SourceContract;
use Config\Exceptions\UnsupportedExtensionException;
use Config\Parsers\JsonParser;
use Config\Parsers\PhpParser;

class Loader implements LoaderContract
{
    /**
     * Supported parsers.
     *
     * @var ParserContract[]
     */
    protected array $parsers;

    /**
     * Constructor.
     *
     * @param ParserContract[] $parsers
     */
    public function __construct(array $parsers = [])
    {
        $this->parsers = $parsers;
    }

    /**
     * Create a Loader with default supported parsers.
     *
     * @return self
     */
    public static function create(): self
    {
        return new self([
            new PhpParser,
            new JsonParser,
        ]);
    }

    /**
     * Load and parse configuration from source.
     *
     * @param SourceContract $source
     * @return array<string, mixed>
     *
     * @throws UnsupportedExtensionException
     */
    public function load(SourceContract $source): array
    {
        $config = [];

        foreach ($source->files() as $file) {
            $parser = $this->parserFor($file['extension']);

            $config[$file['name']] = $parser->parse(
                $file['path']
            );
        }

        return $config;
    }

    /**
     * Retrieve supported parser for file type.
     *
     * @param string $extension
     * @return ParserContract
     *
     * @throws UnsupportedExtensionException
     */
    private function parserFor(string $extension): ParserContract
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($extension)) {
                return $parser;
            }
        }

        throw new UnsupportedExtensionException(
            "Unsupported file extension: {$extension}"
        );
    }
}
