<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpXmlSimple
 * @package Systemcheck\Tests\Shop5
 */
class PhpXmlSimple extends PhpModuleTest
{
    protected string $name = 'SimpleXML';

    protected string $requiredState = 'enabled';

    protected string $description = 'Für JTL-Shop wird die PHP-Erweiterung Simple-XML benötigt.';

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        if (!\extension_loaded('libxml') || !\extension_loaded('simplexml')) {
            return false;
        }

        return \simplexml_load_string('<?xml version="1.0"?><document></document>') instanceof \SimpleXMLElement;
    }
}
