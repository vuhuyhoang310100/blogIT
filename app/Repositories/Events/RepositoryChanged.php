<?php

namespace App\Repositories\Events;

final class RepositoryChanged
{
    public function __construct(public readonly string $namespace) {}
}
