<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpConfigTest;

/**
 * Class PhpMaxExecutionTime
 * @package Systemcheck\Tests\Shop5
 */
class PhpMaxExecutionTime extends PhpConfigTest
{
    protected string $name = 'max_execution_time';

    protected string $requiredState = '>= 120';

    protected string $description = 'Für den Betrieb von JTL-Shop wird eine ausreichend lange Skriptlaufzeit benötigt, '
    . 'damit auch längere Aufgaben (z.B. Newsletterversand) zuverlässig funktionieren.';

    protected bool $isOptional = true;

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $maxExecutionTime   = \ini_get('max_execution_time');
        $this->currentState = $maxExecutionTime;

        return (int)$maxExecutionTime === 0 || $maxExecutionTime >= 120;
    }
}
