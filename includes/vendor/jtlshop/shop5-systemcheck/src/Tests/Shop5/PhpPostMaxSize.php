<?php

declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpConfigTest;

/**
 * Class PhpPostMaxSize
 * @package Systemcheck\Tests\Shop5
 */
class PhpPostMaxSize extends PhpConfigTest
{
    protected string $name = 'post_max_size';

    protected string $requiredState = '>= 8M';

    protected bool $isOptional = true;

    protected bool $isRecommended = true;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $postMaxSize = \ini_get('post_max_size');
        if ($postMaxSize === false) {
            return false;
        }
        $this->currentState = $postMaxSize;

        return $this->shortHandToInt($postMaxSize) >= $this->shortHandToInt('8M');
    }
}
