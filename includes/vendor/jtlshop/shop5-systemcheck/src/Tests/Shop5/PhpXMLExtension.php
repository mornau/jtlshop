<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpXMLExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpXMLExtension extends PhpModuleTest
{
    protected string $name = 'XML';

    protected string $requiredState = 'enabled';

    protected string $description = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-xml</code>.';

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('xml');
    }
}
