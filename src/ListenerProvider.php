<?php

declare(strict_types=1);

namespace Semperton\Events;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
	protected $eventListeners = [];

	public function addListener(string $eventName, callable $listener, int $priority = 0): self
	{
		if ( // check for listener duplicate
			isset($this->eventListeners[$priority][$eventName])
			&& in_array($listener, $this->eventListeners[$priority][$eventName], true)
		) {
			return $this;
		}

		$this->eventListeners[$priority][$eventName][] = $listener;

		return $this;
	}

	public function removeListener(string $eventName, callable $listener, int $priority = 0): self
	{
		if (!isset($this->eventListeners[$priority][$eventName])) {
			return $this;
		}

		foreach ($this->eventListeners[$priority][$eventName] as $index => $entry) {

			if ($listener === $entry) {

				unset($this->eventListeners[$priority][$eventName][$index]);
				break; // we can stop here, because there should be no other (see addListener)
			}
		}

		if (empty($this->eventListeners[$priority][$eventName])) {
			unset($this->eventListeners[$priority][$eventName]);
		}

		return $this;
	}

	public function getListenersForEvent(object $event): iterable
	{
		$priorities = array_keys($this->eventListeners);

		sort($priorities, SORT_NUMERIC);

		foreach ($priorities as $priority) {

			foreach ($this->eventListeners[$priority] as $eventName => $listeners) {

				if ($event instanceof $eventName) {

					foreach ($listeners as $listener) {
						yield $listener;
					}
				}
			}
		}
	}
}
