<?php

namespace Ddd\Event\Tests;

use Ddd\Event\DomainEventInterface;
use Ddd\Event\DomainEventSubscriber;

/**
 * cet espion permettra de simplifer vos tests de publication de vos évènements
 */
class SpySubscriber implements DomainEventSubscriber
{
    public DomainEventInterface $domainEvent;

    public int $handleCallCount = 0;

    /** @var array<DomainEventInterface> */
    public array $traces;

    public function handle(DomainEventInterface $aDomainEvent): bool
    {
        $this->domainEvent = $aDomainEvent;
        $this->handleCallCount++;
        $this->traces[] = $aDomainEvent;
        return true;
    }

    public function isSubscribedTo(DomainEventInterface $aDomainEvent): bool
    {
        return true;
    }
}
