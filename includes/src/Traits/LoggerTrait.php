<?php

declare(strict_types=1);

namespace JTL\Traits;

use JTL\Exceptions\ServiceNotFoundException;
use Monolog\Logger;

/**
 * Trait RoutableTrait
 * @package JTL\Router
 */
trait LoggerTrait
{
    protected ?Logger $logService = null;

    /**
     * @return Logger
     */
    protected function getLogService(): Logger
    {
        if (isset($this->logService) === false) {
            throw new ServiceNotFoundException(Logger::class);
        }
        return $this->logService;
    }

    /**
     * @param string $msg
     * @param array  $param
     * @param string $type
     * @return void
     */
    protected function log(string $msg, array $param = [], string $type = 'info'): void
    {
        if (isset($this->logService)) {
            match ($type) {
                'warning' => $this->getLogService()->warning($msg, $param),
                'error'   => $this->getLogService()->error($msg, $param),
                default   => $this->getLogService()->info($msg, $param)
            };
        }
    }
}
