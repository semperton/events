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

		$listenerArray = &$this->eventListeners[$priority][$eventName];

		$index = array_search($listener, $listenerArray, true);

		if ($index !== false) {

			unset($listenerArray[$index]);

			if (empty($listenerArray)) {
				unset($listenerArray);
			}
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
