<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpConfigTest;

/**
 * Class PhpMemoryLimit
 * @package Systemcheck\Tests\Shop5
 */
class PhpMemoryLimit extends PhpConfigTest
{
    protected const MEMORY_LIMIT_MB = '128';

    protected string $name = 'memory_limit';

    protected string $requiredState = '>= ' . self::MEMORY_LIMIT_MB . 'MB';

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $memoryLimit = \ini_get('memory_limit');
        if ($memoryLimit === false) {
            return false;
        }
        $this->currentState = $memoryLimit;

        return ((int)$memoryLimit === -1
            || $this->shortHandToInt($memoryLimit) >= $this->shortHandToInt(self::MEMORY_LIMIT_MB . 'M'));
    }
}
