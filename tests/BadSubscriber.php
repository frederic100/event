<?php

namespace Ddd\Event\Tests;

use Ddd\Event\DomainEventInterface;
use Ddd\Event\DomainEventSubscriber;

class BadSubscriber implements DomainEventSubscriber
{
    public DomainEventInterface $domainEvent;

    public int $handleCallCount = 0;

    public function handle(DomainEventInterface $aDomainEvent): bool
    {
        throw new \Exception("I am a bad subscriber");
    }

    public function isSubscribedTo(DomainEventInterface $aDomainEvent): bool
    {
        return true;
    }
}
