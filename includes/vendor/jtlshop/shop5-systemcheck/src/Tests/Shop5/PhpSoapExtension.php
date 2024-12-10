<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

/**
 * Class PhpSoapExtension
 * @package Systemcheck\Tests\Shop5
 */
class PhpSoapExtension extends PhpModuleTest
{
    protected string $name = 'SOAP';

    protected string $requiredState = 'enabled';

    protected string $description = 'Die Prüfung der Umsatzsteuer-ID erfolgt per "MwSt-Informationsaustauschsystem '
    . '(MIAS) der Europäischen Kommission".<br> Dieses System wird mit dem Übertragungsprotokoll "SOAP" abgefragt, '
    . 'was eine entsprechende PHP-Unterstützung voraussetzt.';

    protected bool $isOptional = true;

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \class_exists('SoapClient');
    }
}
