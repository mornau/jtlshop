<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpJsonExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpJsonExtension extends PhpModuleTest
{
    protected string $name = 'JSON';

    protected string $requiredState = 'enabled';

    protected string $description = 'JTL-Shop benötigt PHP-Unterstützung für das JSON-Format.';

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('json');
    }
}
