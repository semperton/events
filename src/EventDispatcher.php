<?php

declare(strict_types=1);

namespace Semperton\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

final class EventDispatcher implements EventDispatcherInterface
{
	/** @var ListenerProviderInterface */
	protected $listenerProvider;

	public function __construct(ListenerProviderInterface $listenerProvider)
	{
		$this->listenerProvider = $listenerProvider;
	}

	public function dispatch(object $event): object
	{
		$stoppable = $event instanceof StoppableEventInterface;

		/** @var StoppableEventInterface $event */
		if ($stoppable && $event->isPropagationStopped()) {
			return $event;
		}

		$listeners = $this->listenerProvider->getListenersForEvent($event);

		/** @var callable */
		foreach ($listeners as $listener) {

			$listener($event);

			if ($stoppable && $event->isPropagationStopped()) {
				break;
			}
		}

		return $event;
	}
}
