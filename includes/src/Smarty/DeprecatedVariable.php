<?php

declare(strict_types=1);

namespace JTL\Smarty;

/**
 * Class DeprecatedVariable
 * @package \JTL\Smarty
 */
class DeprecatedVariable
{
    public bool $nocache = false;

    public function __construct(
        private readonly mixed $value,
        private readonly string $name,
        private readonly string $version
    ) {
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if ($name === 'value') {
            \trigger_error(
                'Smarty variable ' . $this->name . ' is deprecated since JTL-Shop version ' . $this->version . '.',
                \E_USER_DEPRECATED
            );

            return $this->value;
        }

        return null;
    }
}
