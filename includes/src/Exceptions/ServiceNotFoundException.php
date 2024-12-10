<?php

declare(strict_types=1);

namespace JTL\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ServiceNotFoundException
 * @package JTL\Exceptions
 */
class ServiceNotFoundException extends \Exception implements NotFoundExceptionInterface
{
    /**
     * ServiceNotFoundException constructor.
     * @param string $interface
     */
    public function __construct(protected string $interface)
    {
        parent::__construct('The Service "' . $interface . '" could not be found.');
    }
}
