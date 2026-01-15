<?php

namespace App\Repositories\Listeners;

use App\Repositories\Cache\RepositoryCacheInvalidator;
use App\Repositories\Events\RepositoryChanged;

final class InvalidateRepositoryCache
{
    public function __construct(private readonly RepositoryCacheInvalidator $invalidator) {}

    public function handle(RepositoryChanged $event): void
    {
        $this->invalidator->invalidate($event->namespace);
    }
}
