<?php

namespace Ddd\Event;

interface DomainEventSubscriber
{
    /**
     * @return bool handled event. true si l'evenement a été traité.
     */
    public function handle(DomainEventInterface $aDomainEvent): bool;

    public function isSubscribedTo(DomainEventInterface $aDomainEvent): bool;
}
