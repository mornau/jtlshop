<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpGdExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpGdExtension extends PhpModuleTest
{
    protected string $name = 'GD';

    protected string $requiredState = 'enabled';

    protected string $description = 'Wird zur Bildbe- und Verarbeitung benötigt.';

    protected string $isReplaceableBy = PhpImagickExtension::class;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('gd');
    }
}
