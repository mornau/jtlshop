<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpBCMathExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpBCMathExtension extends PhpModuleTest
{
    protected string $name = 'BCMath';

    protected string $requiredState = 'enabled';

    protected string $description = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-bcmath</code> '
    . 'für diverse Berechnungen.';

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('bcmath');
    }
}
