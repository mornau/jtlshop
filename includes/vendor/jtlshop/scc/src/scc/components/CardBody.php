<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;

/**
 * Class CardBody
 * @package scc\components
 */
class CardBody extends AbstractBlockComponent
{
    /**
     * CardBody constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cardbody.tpl');
        $this->setName('cardbody');
        $this->addParam(new ComponentProperty('footer'));
        $this->addParam(new ComponentProperty('footer-tag', 'div'));
        $this->addParam(new ComponentProperty('footer-bg-variant'));
        $this->addParam(new ComponentProperty('footer-border-variant'));
        $this->addParam(new ComponentProperty('footer-text-variant'));
        $this->addParam(new ComponentProperty('footer-class'));
    }
}
