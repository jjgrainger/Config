<?php

namespace Config;

use Config\Contracts\SourceContract;
use Config\Exceptions\InvalidSourceException;
use DirectoryIterator;

class Source implements SourceContract
{
    /**
     * Source location
     *
     * @param string $location
     */
    protected readonly string $location;

    /**
     * Constructor
     *
     * @param string $location
     *
     * @throws InvalidSourceException
     */
    public function __construct(string $location)
    {
        if (!file_exists($location)) {
            throw new InvalidSourceException(
                "Location does not exist: {$location}"
            );
        }

        if (!is_file($location) && !is_dir($location)) {
            throw new InvalidSourceException(
                "Location must be a file or directory: {$location}"
            );
        }

        $this->location = $location;
    }

    /**
     * Return discovered files and their metadata.
     *
     * @return array<int, array{
     *     path: string,
     *     name: string,
     *     extension: string
     * }>
     */
    public function files(): array
    {
        $files = [];

        if (is_file($this->location)) {
            $files[] = $this->metadata($this->location);

            return $files;
        }

        foreach (new DirectoryIterator($this->location) as $file) {
            if ($file->isDot() || !$file->isFile()) {
                continue;
            }

            $files[] = $this->metadata($file->getPathname());
        }

        return $files;
    }

    /**
     * Create file metadata.
     *
     * @param string $path
     * @return array{
     *     path: string,
     *     name: string,
     *     extension: string
     * }
     */
    protected function metadata(string $path): array
    {
        return [
            'path' => $path,
            'name' => pathinfo($path, PATHINFO_FILENAME),
            'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
        ];
    }
}
