<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpIconvExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpIconvExtension extends PhpModuleTest
{
    protected string $name = 'Iconv';

    protected string $requiredState = 'enabled';

    protected string $description = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-iconv</code> '
    . 'für die Internationalisierung.';

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('iconv');
    }
}
