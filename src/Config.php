<?php

namespace Config;

use Config\Contracts\ConfigContract;
use Config\Contracts\LoaderContract;
use Config\Source;

class Config implements ConfigContract
{
    /**
     * Configuration
     *
     * @var array<string, mixed>
     */
    protected readonly array $config;

    /**
     * Constructor
     *
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Load configuration from file or directory.
     *
     * @param string $path
     * @param LoaderContract|null $loader
     * @return self
     */
    public static function load(string $path, ?LoaderContract $loader = null): self
    {
        $loader = $loader ?? Loader::create();

        return new self($loader->load(new Source($path)));
    }

    /**
     * Return a configuration value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->config;

        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }

            $value = $value[$part];
        }

        return $value;
    }

    /**
     * Return true if value exists by key, false if not
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool
    {
        $value = $this->config;

        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return false;
            }

            $value = $value[$part];
        }

        return true;
    }

    /**
     * Return the configuration array
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->config;
    }
}
