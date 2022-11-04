<?php

declare(strict_types=1);

namespace Semperton\Events;

class Event implements EventInterface
{
	protected bool $propagationStopped = false;

	public function stopPropagation(): void
	{
		$this->propagationStopped = true;
	}

	public function isPropagationStopped(): bool
	{
		return $this->propagationStopped;
	}
}
