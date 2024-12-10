<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;
use scc\ComponentPropertyType;

/**
 * Class Link
 * @package scc\components
 */
class Link extends AbstractBlockComponent
{
    /**
     * Link constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('link.tpl');
        $this->setName('link');

        $this->addParam(new ComponentProperty('rel'));
        $this->addParam(new ComponentProperty('target'));
        $this->addParam(new ComponentProperty('href', '#'));

        $prop = new ComponentProperty('name');
        $prop->setIsRequired(true);
        $this->addParam($prop);
    }
}
