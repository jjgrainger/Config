<?php

namespace Config\Contracts;

interface ConfigContract
{
    public function get(string $key, mixed $default = null): mixed;

    public function has(string $key): bool;

    /**
     * Return the configuration array
     *
     * @return array<string, mixed>
     */
    public function all(): array;
}
