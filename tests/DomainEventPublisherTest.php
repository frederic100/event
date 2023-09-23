<?php

namespace Ddd\Event\Tests;

use Ddd\Event\DomainEventPublisher;
use DomainException;
use PHPUnit\Framework\TestCase;

class DomainEventPublisherTest extends TestCase
{
    protected function setUp(): void
    {
        DomainEventPublisher::tearDown();
    }

    public function testSubscribe(): void
    {
        $subscriber = new SpySubscriber();

        $id = DomainEventPublisher::instance()->subscribe($subscriber);
        $this->assertSame(0, $id);

        $id = DomainEventPublisher::instance()->subscribe($subscriber);
        $this->assertSame(1, $id);
    }

    public function testPublishWithDistributeImmediatly(): void
    {
        DomainEventPublisher::instance()->distributeImmmediatly();

        $subscriber = new SpySubscriber();
        $id = DomainEventPublisher::instance()->subscribe($subscriber);

        $event = new EventSent("unId");
        DomainEventPublisher::instance()->publish($event);

        $this->assertInstanceOf(EventSent::class, $subscriber->domainEvent);
        /** @var EventSent $es */
        $es = $subscriber->domainEvent;
        $this->assertEquals("unId", $es->id());
    }

    public function testUnsubscribe(): void
    {
        $subscriber = new SpySubscriber();
        $id = DomainEventPublisher::instance()->subscribe($subscriber);
        DomainEventPublisher::instance()->unsubscribe($id);
        $event = new EventSent("unId");
        DomainEventPublisher::instance()->publish($event);
        $this->assertFalse(isset($subscriber->domainEvent));
    }

    public function testExceptionOnCloneAttempt(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $publisher = DomainEventPublisher::instance();
        $leClone = clone $publisher;
    }

    public function testCreateNotInitializedInstance(): void
    {
        $subscriber = new SpySubscriber();

        $id = DomainEventPublisher::instance()->subscribe($subscriber);

        $this->assertSame(0, $id);

        DomainEventPublisher::tearDown();

        $subscriber = new SpySubscriber();

        $id = DomainEventPublisher::instance()->subscribe($subscriber);
        $this->assertSame(0, $id);
    }

    public function testDistributeOneEvent(): void
    {
        $event = new EventSent("unId");

        $eventSubscriber = new SpySubscriber();
        $id = DomainEventPublisher::instance()->subscribe($eventSubscriber);

        DomainEventPublisher::instance()->publish($event);

        $this->assertFalse(isset($eventSubscriber->domainEvent));

        DomainEventPublisher::instance()->distribute();

        $this->assertInstanceOf(EventSent::class, $eventSubscriber->domainEvent);
    }

    public function testDistributeTreeEvents(): void
    {
        $event1 = new EventSent("unId");
        $event2 = new EventSent("id2");
        $event3 = new EventSent("id3");

        $eventSubscriber = new SpySubscriber();
        $id = DomainEventPublisher::instance()->subscribe($eventSubscriber);

        DomainEventPublisher::instance()->publish($event1);
        DomainEventPublisher::instance()->publish($event2);
        DomainEventPublisher::instance()->publish($event3);

        DomainEventPublisher::instance()->distribute();

        $this->assertEquals(3, $eventSubscriber->handleCallCount);
        $this->assertEquals($event1, $eventSubscriber->traces[0]);
        $this->assertEquals($event2, $eventSubscriber->traces[1]);
        $this->assertEquals($event3, $eventSubscriber->traces[2]);

        DomainEventPublisher::instance()->distribute();
        $this->assertEquals(3, $eventSubscriber->handleCallCount);
    }

    public function testDistributionIdempotent(): void
    {
        $event = new EventSent("unId");

        $eventSubscriber = new SpySubscriber();
        $id = DomainEventPublisher::instance()->subscribe($eventSubscriber);

        DomainEventPublisher::instance()->distributeImmmediatly();

        DomainEventPublisher::instance()->publish($event);
        DomainEventPublisher::instance()->distribute();

        $this->assertEquals(1, $eventSubscriber->handleCallCount);
    }

    public function testABadSubscriberMustNotStopDistribute(): void
    {
        DomainEventPublisher::instance()->distributeImmmediatly();

        $event = new EventSent("unId");
        $event2 = new EventSent("unId2");

        $badSubscriber = new BadSubscriber();
        DomainEventPublisher::instance()->subscribe($badSubscriber);

        $eventSubscriber = new SpySubscriber();
        DomainEventPublisher::instance()->subscribe($eventSubscriber);

        DomainEventPublisher::instance()->publish($event);
        DomainEventPublisher::instance()->publish($event2);
        DomainEventPublisher::instance()->distribute();

        $this->assertEquals(2, $eventSubscriber->handleCallCount);
    }
}
