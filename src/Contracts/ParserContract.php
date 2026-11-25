<?php

namespace Config\Contracts;

interface ParserContract
{
    /**
     * Parse configuration file.
     *
     * @param string $file
     * @return array<string, mixed>
     */
    public function parse(string $file): array;

    /**
     * Return true if file extension is supported.
     *
     * @param string $extension
     * @return boolean
     */
    public function supports(string $extension): bool;
}
