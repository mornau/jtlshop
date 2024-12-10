<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpMbstringExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpMbstringExtension extends PhpModuleTest
{
    protected string $name = 'mbstring';

    protected string $requiredState = 'enabled';

    protected string $description = 'Die <code>mbstring</code>-Erweiterung ist zum Betrieb des '
    . 'JTL-Shop zwingend erforderlich.';

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('mbstring');
    }
}
