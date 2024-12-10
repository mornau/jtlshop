<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpPdoMysqlSupport
 * @package Systemcheck\Tests\Shop5
 */
class PhpPdoMysqlSupport extends PhpModuleTest
{
    protected string $name = 'PDO::MySQL';

    protected string $requiredState = 'enabled';

    protected string $description = 'Für JTL-Shop wird die Unterstützung für PHP-Data-Objects ' .
    '(<code>php-pdo</code> und <code>php-mysql</code>) benötigt.';

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('pdo') && \extension_loaded('pdo_mysql');
    }
}
