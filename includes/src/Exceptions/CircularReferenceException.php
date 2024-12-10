<?php

declare(strict_types=1);

namespace JTL\Exceptions;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class CircularReferenceException
 * @package JTL\Exceptions
 */
class CircularReferenceException extends \Exception implements ContainerExceptionInterface
{
    /**
     * CircularReferenceException constructor.
     * @param string $interface
     */
    public function __construct(protected string $interface)
    {
        parent::__construct('Circular reference for "' . $interface . '" detected.');
    }
}
