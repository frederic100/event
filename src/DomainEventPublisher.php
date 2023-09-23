<?php

namespace Ddd\Event;

final class DomainEventPublisher
{
    /** @var array<int,DomainEventSubscriber> $subscribers */
    private array $subscribers;
    private static ?DomainEventPublisher $instance = null;

    /** @var array<DomainEventInterface> */
    private array $eventToDistribute;

    private bool $distributeImmediatly = false;


    private function __construct()
    {
        $this->subscribers = [];
        $this->eventToDistribute = [];
    }

    public static function instance(): DomainEventPublisher
    {
        if (null === static::$instance) {
            static::$instance = new DomainEventPublisher();
        };
        return static::$instance;
    }

    public function distributeImmmediatly(): void
    {
        $this->distributeImmediatly = true;
    }

    public function __clone()
    {
        throw new \BadMethodCallException('Clone is not supported');
    }

    /**
     * @return int index du subscriber
     */
    public function subscribe(
        DomainEventSubscriber $aDomainEventSubscriber
    ): int {
        $this->subscribers[] = $aDomainEventSubscriber;
        return count($this->subscribers) - 1;
    }

    public function publish(DomainEventInterface $anEvent): void
    {
        $this->eventToDistribute[] =  $anEvent;
        if ($this->distributeImmediatly) {
            $this->distribute();
        }
    }

    public function distribute(): void
    {
        foreach ($this->eventToDistribute as $index => $event) {
            $this->distributeEventToSubscribers($event);
            $this->unsetEventByIndex($index);
        }
    }

    private function distributeEventToSubscribers(DomainEventInterface $event): void
    {
        foreach ($this->subscribers as $aSubscriber) {
            $this->tryToHandleEventIfSubscribed($aSubscriber, $event);
        }
    }

    private function tryToHandleEventIfSubscribed(DomainEventSubscriber $subscriber, DomainEventInterface $event): void
    {
        if ($subscriber->isSubscribedTo($event)) {
            $this->tryToHandleEvent($subscriber, $event);
        }
    }

    private function tryToHandleEvent(DomainEventSubscriber $subscriber, DomainEventInterface $event): void
    {
        try {
            $subscriber->handle($event);
        } catch (\Exception $e) {
        }
    }

    private function unsetEventByIndex(int $index): void
    {
        unset($this->eventToDistribute[$index]);
    }

    /**
     * @param int $id index du subscriber
     */
    public function unsubscribe(int $id): void
    {
        unset($this->subscribers[$id]);
    }

    public static function tearDown(): void
    {
        static::$instance = null;
    }
}
