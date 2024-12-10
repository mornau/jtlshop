<?php declare(strict_types=1);

namespace scc\components;

/**
 * Class CSRFToken
 * @package scc\components
 */
class CSRFToken extends AbstractFunctionComponent
{
    /**
     * CSRFToken constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('csrf_token.tpl');
        $this->setName('csrf_token');
    }
}
