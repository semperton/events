<?php

declare(strict_types=1);

namespace Semperton\Events;

use Psr\EventDispatcher\StoppableEventInterface;

interface EventInterface extends StoppableEventInterface
{
	public function stopPropagation(): void;
}
