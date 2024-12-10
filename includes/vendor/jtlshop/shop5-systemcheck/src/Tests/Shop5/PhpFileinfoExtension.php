<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpModuleTest;

class PhpFileinfoExtension extends PhpModuleTest
{
    protected string $name = 'fileinfo';

    protected string $requiredState = 'enabled';

    protected string $description = 'Die Erweiterung wird genutzt um Dateityp und Kodierung einer Datei '
    . 'zu ermitteln. z.B. für die Bilderschnittstelle oder im Filecheck.';

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        return \extension_loaded('fileinfo');
    }
}
