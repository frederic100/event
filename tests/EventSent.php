<?php

namespace Ddd\Event\Tests;

use Ddd\Event\DomainEventInterface;
use Safe\DateTimeImmutable;

/**
 * EventSended : nom + verbe au passé pour nommer vos evennements
 */
class EventSent implements DomainEventInterface
{
    private string $id;
    private DateTimeImmutable $occurredOn;

    public function __construct(string $id, DateTimeImmutable $occuredOn = new DateTimeImmutable())
    {
        $this->id = $id;
        $this->occurredOn = $occuredOn;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
