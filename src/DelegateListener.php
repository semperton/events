<?php

declare(strict_types=1);

namespace Semperton\Events;

use RuntimeException;

use function is_callable;

final class DelegateListener
{
	/** @var callable */
	protected $resolver;

	protected string $className;

	protected ?string $methodName;

	public function __construct(
		callable $resolver,
		string $className,
		?string $methodName = null
	) {
		$this->resolver = $resolver;
		$this->className = $className;
		$this->methodName = $methodName;
	}

	public function __invoke(object $event): void
	{
		$className = $this->className;
		$methodName = $this->methodName;

		$instance = ($this->resolver)($className);

		if ($methodName !== null) {
			if (!is_callable([$instance, $methodName])) {
				throw new RuntimeException("$className::$methodName() is not callable");
			}
			$instance->{$methodName}($event);
		} else {
			if (!is_callable($instance)) {
				throw new RuntimeException("$className::__invoke() is not callable");
			}
			$instance($event);
		}
	}
}
