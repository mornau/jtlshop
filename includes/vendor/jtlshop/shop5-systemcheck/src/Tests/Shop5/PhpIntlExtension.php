<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpIntlExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpIntlExtension extends PhpModuleTest
{
    protected string $name = 'Intl';

    protected string $requiredState = 'enabled';

    protected string $description = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-intl</code> '
    . 'für die Internationalisierung.';

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('intl') && \defined('INTL_IDNA_VARIANT_UTS46');
    }
}
