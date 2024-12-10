<?php

declare(strict_types=1);

namespace Systemcheck\Tests;

/**
 * Class PhpConfigTest
 * @package Systemcheck\Tests
 */
abstract class PhpConfigTest extends AbstractTest
{
    /**
     * @param string $shorthand
     * @return float|int|string
     */
    protected function shortHandToInt(string $shorthand)
    {
        return match (\substr($shorthand, -1)) {
            'M', 'm' => (int)$shorthand * 1048576,
            'K', 'k' => (int)$shorthand * 1024,
            'G', 'g' => (int)$shorthand * 1073741824,
            default  => $shorthand,
        };
    }
}
