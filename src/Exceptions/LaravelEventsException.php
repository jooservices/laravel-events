<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Exceptions;

use JOOservices\Exceptions\Base\AbstractContextAwareLogicException;

/**
 * Root marker for programmer / domain-invariant failures in this package.
 */
abstract class LaravelEventsException extends AbstractContextAwareLogicException
{
}
