<?php

namespace Ddd\Event;

use Safe\DateTimeImmutable;

interface DomainEventInterface
{
    public function occurredOn(): DateTimeImmutable;
}
