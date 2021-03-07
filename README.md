<div align="center">
<a href="https://github.com/semperton">
<img src="https://avatars0.githubusercontent.com/u/76976189?s=140" alt="Semperton">
</a>
<h1>Semperton Events</h1>
<p>A minimal, PSR-14 compilant events library.</p>
//
</div>

<hr>

## Installation

Just use Composer:

```
composer require semperton/events
```
Routing requires PHP 7.1+

## Adding listeners

Listeners are added with the event class or interface name, a listener callback and an optinal priority value (default is 0). The listener callback can be any PHP callable and should accept only one argument - the event object.
```php
use Semperton\Events\ListenerProvider;

interface EventInterface{}
final class TestEvent implements EventInterface
{
	public $message = '';
}

$listeners = new ListenerProvider();

$listeners->addListener(TestEvent::class, function (TestEvent $event) {
	$event->message .= ' World';
}, 5);

$listeners->addListener(EventInterface::class, function (EventInterface $event) {
	$event->message = 'Hello';
}, 1); // gets called first, because of priority 1
```
If you register a listener with an interface name, that listener will also be triggered, if the dispatched event implements said interface.

## Removing listeners

Remove listeners with the ```removeListener()``` method. Event name, listener and priority must match.
```php
$myListener = function (TestEvent $event) {};

$listeners->addListener(TestEvent::class, $myListener, 7);
$listeners->removeListener(TestEvent::class, $myListener, 7); // priority must match
```

## Dispatching

All you need is a ```ListenerProvider``` (collection of event listeners) and an ```EventDispatcher```.
```php
use Semperton\Events\EventDispatcher;

$dispatcher = new EventDispatcher($listeners);

$event = new TestEvent();

/** @var TestEvent */
$dispatchedEvent = $dispatcher->dispatch($event);

$dispatchedEvent === $event; // true
$dispatchedEvent->message; // Hello World

```

## Lazy listener example

Coming soon...
```php
	
```