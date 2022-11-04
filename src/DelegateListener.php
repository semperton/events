<?php

declare(strict_types=1);

namespace Semperton\Events;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;

use function is_callable;

final class DelegateListener
{
	protected ContainerInterface $container;

	protected string $className;

	protected ?string $methodName;

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
		$className = $this->className;
		$methodName = $this->methodName;

		$instance = $this->container->get($className);

		if ($methodName !== null) {
			if (!is_callable([$instance, $methodName])) {
				throw new InvalidArgumentException("< $methodName > of < $className > is not callable");
			}
			$instance->{$methodName}($event);
		} else {
			if (!is_callable($instance)) {
				throw new InvalidArgumentException("< $className > is not callable");
			}
			$instance($event);
		}
	}
}
