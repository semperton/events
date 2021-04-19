<?php

declare(strict_types=1);

namespace Semperton\Events;

class Event implements EventInterface
{
	/** @var bool */
	protected $propagationStopped = false;

	public function stopPropagation(): void
	{
		$this->propagationStopped = true;
	}

	public function isPropagationStopped(): bool
	{
		return $this->propagationStopped;
	}
}
