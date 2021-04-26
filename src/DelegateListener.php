<?php

declare(strict_types=1);

namespace Semperton\Events;

use Psr\Container\ContainerInterface;
use RuntimeException;

final class DelegateListener
{
	/** @var ContainerInterface */
	protected $container;

	/** @var string */
	protected $className;

	/** @var null|string */
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
		$className = $this->className;
		$methodName = $this->methodName;

		$instance = $this->container->get($className);

		if ($methodName !== null) {

			if (!is_callable([$instance, $methodName])) {
				throw new RuntimeException("< $methodName > of < $className > is not callable");
			}

			$instance->{$methodName}($event);
			return;
		}

		if (!is_callable($instance)) {
			throw new RuntimeException("< $className > is not callable");
		}

		$instance($event);
	}
}
