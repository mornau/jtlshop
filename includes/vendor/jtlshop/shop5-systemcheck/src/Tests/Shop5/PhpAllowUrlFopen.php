<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpConfigTest;

/**
 * Class PhpAllowUrlFopen
 * @package Systemcheck\Tests\Shop5
 */
class PhpAllowUrlFopen extends PhpConfigTest
{
    protected string $name = 'allow_url_fopen';

    protected string $requiredState = 'on';

    protected string $description = 'Wird benötigt um Dateien auf entfernten Systemen zu öffnen '
    . 'und zu bearbeiten. (optional)';

    protected bool $isOptional = true;

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $allowUrlFopen      = (bool)\ini_get('allow_url_fopen');
        $this->currentState = $allowUrlFopen ? 'on' : 'off';

        return $allowUrlFopen === true;
    }
}
