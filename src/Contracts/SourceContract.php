<?php

namespace Config\Contracts;

interface SourceContract
{
    /**
     * Return discovered files and their metadata.
     *
     * @return array<int, array{
     *     path: string,
     *     name: string,
     *     extension: string
     * }>
     */
    public function files(): array;
}
