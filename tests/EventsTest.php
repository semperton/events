<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Semperton\Events\EventDispatcher;
use Semperton\Events\ListenerProvider;

interface CustomEvent
{
	public function setMessage(string $message): void;
	public function getMessage(): string;
}

final class TestEvent implements CustomEvent
{
	protected $message = '';

	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	public function getMessage(): string
	{
		return $this->message;
	}
}

final class EventsTest extends TestCase
{
	public function testListenerProvider()
	{
		$provider = new ListenerProvider();

		$event = new TestEvent();
		$listener1 = static function () {
		};
		$listener2 = static function () {
		};

		$provider->addListener(TestEvent::class, $listener1);

		$provider->addListener(CustomEvent::class, $listener2, -1);

		$this->assertSame([$listener2, $listener1], iterator_to_array($provider->getListenersForEvent($event)));

		$provider->removeListener(TestEvent::class, $listener1);

		$this->assertSame([$listener2], iterator_to_array($provider->getListenersForEvent($event)));

		$provider->removeListener(CustomEvent::class, $listener2, -1);

		$this->assertEmpty(iterator_to_array($provider->getListenersForEvent($event)));
	}

	public function testEventDispatcher()
	{
		$provider = new ListenerProvider();
		$dispatcher = new EventDispatcher($provider);

		$event = new TestEvent();

		$provider->addListener(CustomEvent::class, static function (CustomEvent $event) {
			$event->setMessage($event->getMessage() . ' World');
		}, 2);

		$provider->addListener(TestEvent::class, static function (TestEvent $event) {
			$event->setMessage('Hello');
		});

		/** @var TestEvent */
		$dispatchedEvent = $dispatcher->dispatch($event);

		$this->assertEquals('Hello World', $dispatchedEvent->getMessage());
	}
}
