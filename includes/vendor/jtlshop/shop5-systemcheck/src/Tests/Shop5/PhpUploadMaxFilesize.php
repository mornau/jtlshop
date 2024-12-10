<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpConfigTest;

/**
 * Class PhpUploadMaxFilesize
 * @package Systemcheck\Tests\Shop5
 */
class PhpUploadMaxFilesize extends PhpConfigTest
{
    protected string $name = 'upload_max_filesize';

    protected string $requiredState = '>= 6M';

    protected bool $isOptional = true;

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $uploadMaxFilesize  = \ini_get('upload_max_filesize');
        if ($uploadMaxFilesize === false) {
            return false;
        }
        $this->currentState = $uploadMaxFilesize;

        return $this->shortHandToInt($uploadMaxFilesize) >= $this->shortHandToInt('6M');
    }
}
