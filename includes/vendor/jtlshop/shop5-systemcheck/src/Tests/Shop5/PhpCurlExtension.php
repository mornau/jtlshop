<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpCurlExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpCurlExtension extends PhpModuleTest
{
    protected string $name = 'cURL';

    protected string $requiredState = 'enabled';

    protected string $description = 'Wird benötigt um Webrequests auszuführen. (optional)';

    protected bool $isOptional = true;

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('curl');
    }
}
