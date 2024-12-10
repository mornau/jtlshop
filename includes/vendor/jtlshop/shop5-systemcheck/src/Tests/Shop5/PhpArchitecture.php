<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\ProgramTest;

/**
 * Class PhpArchitecture
 * @package Systemcheck\Tests\Shop5
 */
class PhpArchitecture extends ProgramTest
{
    protected string $name = 'Architektur';

    protected string $requiredState = '64bit';

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $is64bits           = \PHP_INT_SIZE === 8;
        $this->currentState = $is64bits ? '64bit' : '32bit';

        return $is64bits === true;
    }
}
