<?php

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Semperton\Container\Container;
use Semperton\Events\EventDispatcher;
use Semperton\Events\ListenerProvider;

require_once __DIR__ . '/../vendor/autoload.php';

final class DelegateListener
{
	protected $container;
	protected $className;
	protected $methodName;

	public function __construct(ContainerInterface $container, string $className, ?string $methodName = null)
	{
		$this->container = $container;
		$this->className = $className;
		$this->methodName = $methodName;
	}

	public function __invoke(object $event): void
	{
		$instance = $this->container->get($this->className);

		if ($this->methodName !== null) {
			if (is_callable([$instance, $this->methodName])) {
				$instance->{$this->methodName}($event);
			}
		} else if (is_callable($instance)) {
			$instance($event);
		}
	}
}

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

final class DelegateListenerTest extends TestCase
{
	public function testEventDelegation()
	{
		$provider = new ListenerProvider();
		$dispatcher = new EventDispatcher($provider);

		$container = new Container();
		$delegateListener1 = new DelegateListener($container, Service::class);
		$delegateListener2 = new DelegateListener($container, Service::class, 'action');

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
}
