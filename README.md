<div align="center">
<a href="https://github.com/semperton">
<img src="https://avatars0.githubusercontent.com/u/76976189?s=140" alt="Semperton">
</a>
<h1>Semperton Events</h1>
<p>A minimal, PSR-14 compilant event library.</p>
//
</div>

<hr>

## Installation

Just use Composer:

```
composer require semperton/events
```
Events requires PHP 7.1+

## Adding listeners

Listeners are added with an event class or interface name, a listener callback and an optional priority value (default is 0, negative values allowed). The listener callback can be any PHP callable and should accept only one argument - the event object.
```php
use Semperton\Events\ListenerProvider;

interface EventInterface{}
final class TestEvent implements EventInterface
{
	public $message = '';
}

$provider = new ListenerProvider();

$provider->addListener(TestEvent::class, function (TestEvent $event) {
	$event->message .= ' World';
});

$provider->addListener(EventInterface::class, function (EventInterface $event) {
	$event->message = 'Hello';
}, -1); // gets called first, because of higher priority (negatives allowed)
```
If you register a listener with an interface name, that listener will also be triggered if the dispatched event implements said interface.

## Removing listeners

Remove listeners with the ```removeListener()``` method. Event name, listener and priority must match.
```php
$myListener = function (TestEvent $event) {};

$provider->addListener(TestEvent::class, $myListener, 7);
$provider->removeListener(TestEvent::class, $myListener, 7); // priority must match too
```

## Dispatching

All you need is a ```ListenerProvider``` (collection of event listeners) and an ```EventDispatcher```.
```php
use Semperton\Events\EventDispatcher;

// using the previously created ListenerProvider
$dispatcher = new EventDispatcher($provider);

$event = new TestEvent();

/** @var TestEvent */
$dispatchedEvent = $dispatcher->dispatch($event);

$dispatchedEvent === $event; // true
$dispatchedEvent->message; // 'Hello World'
```

## DelegateListener example

If you want to call service methods in response to events, you may use a ```ContainerInterface``` to resolve your services, etc. For this purpose, a ```DelegateListener``` can be helpful:
```php
final class DelegateListener
{
	protected $container;
	protected $className;
	protected $methodName;

	public function __construct(
		ContainerInterface $container,
		string $className,
		?string $methodName = null
	) {
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

$listener = new DelegateListener($container, Service::class, 'method');
$provider->addListener(TestEvent::class, $listener);
```