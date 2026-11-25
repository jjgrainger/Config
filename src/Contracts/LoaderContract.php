<?php

namespace Config\Contracts;

interface LoaderContract
{
    /**
     * Load configuration from source.
     *
     * @param SourceContract $source
     * @return array<string, mixed>
     */
    public function load(SourceContract $source): array;
}
