<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpSplSupport
 * @package Systemcheck\Tests\Shop5
 */
class PhpSplSupport extends PhpModuleTest
{
    protected string $name = 'Standard PHP Library';

    protected string $requiredState = 'enabled';

    protected string $description = 'Für JTL-Shop5 wird Unterstützung für die Standard PHP Library (SPL) benötigt.';

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('SPL') && \function_exists('spl_autoload_register');
    }
}
