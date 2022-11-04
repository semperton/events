<?php

use PHPUnit\Framework\TestCase;
use Semperton\Container\Container;
use Semperton\Events\DelegateListener;
use Semperton\Events\EventDispatcher;
use Semperton\Events\ListenerProvider;

final class Service
{
	public function __invoke(TestEvent $event): void
	{
		$event->setMessage('Invoke');
	}

	public function action(TestEvent $event): void
	{
		$event->setMessage('Method');
	}
}

final class Resolver
{
	public function __invoke(string $class): object
	{
		return new $class();
	}
}

final class DelegateListenerTest extends TestCase
{
	public function testEventDelegation()
	{
		$provider = new ListenerProvider();
		$dispatcher = new EventDispatcher($provider);

		$resolver = new Resolver();

		$delegateListener1 = new DelegateListener($resolver, Service::class);
		$delegateListener2 = new DelegateListener($resolver, Service::class, 'action');

		$provider->addListener(TestEvent::class, $delegateListener1);

		$event = new TestEvent();

		/** @var TestEvent */
		$event = $dispatcher->dispatch($event);

		$this->assertEquals('Invoke', $event->getMessage());

		$provider->addListener(TestEvent::class, $delegateListener2);

		/** @var TestEvent */
		$event = $dispatcher->dispatch($event);

		$this->assertEquals('Method', $event->getMessage());
	}

	public function testMethodNotCallable(): void
	{
		$this->expectException(RuntimeException::class);
		$this->expectErrorMessage('Service::test() is not callable');

		$resolver = new Resolver();
		$delegateListener = new DelegateListener($resolver, Service::class, 'test');
		$delegateListener->__invoke(new TestEvent());
	}
}
