<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpCalendarExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpCalendarExtension extends PhpModuleTest
{
    protected string $name = 'calendar';

    protected string $requiredState = 'enabled';

    protected string $description = 'Wird für die Konvertierung zwischen Kalenderformaten benötigt.';

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('calendar');
    }
}
