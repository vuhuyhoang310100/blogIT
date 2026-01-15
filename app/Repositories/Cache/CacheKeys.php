<?php

namespace App\Repositories\Cache;

final class CacheKeys
{
    public function __construct(
        private readonly string $prefix,
        private readonly string $namespace
    ) {}

    public function versionKey(): string
    {
        return "{$this->prefix}:{$this->namespace}:v";
    }

    public function make(string $method, array $parts = []): string
    {
        $normalized = array_map(
            fn ($v) => is_scalar($v) ? (string) $v : md5(serialize($v)),
            $parts
        );

        return implode(':', array_filter([$this->prefix, $this->namespace, $method, ...$normalized]));
    }

    public function tag(): string
    {
        return "{$this->prefix}:{$this->namespace}";
    }
}
