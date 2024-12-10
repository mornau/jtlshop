<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;
use scc\ComponentPropertyType;

/**
 * Class Input
 * @package scc\components
 */
class Input extends AbstractFunctionComponent
{
    /**
     * Input constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('input.tpl');
        $this->setName('input');

        $prop = new ComponentProperty('type', 'text', ComponentPropertyType::TYPE_STRING);
        $prop->setIsRequired(true);
        $this->addParam($prop);

        $this->addParam(new ComponentProperty('value'));
        $this->addParam(new ComponentProperty('placeholder'));
        $this->addParam(new ComponentProperty('name'));
        $this->addParam(new ComponentProperty('autocomplete'));
        $this->addParam(new ComponentProperty('step', null, ComponentPropertyType::TYPE_NUMERIC));
        $this->addParam(new ComponentProperty('maxlength', null, ComponentPropertyType::TYPE_NUMERIC));
        $this->addParam(new ComponentProperty('size', null, ComponentPropertyType::TYPE_INT));
        $this->addParam(new ComponentProperty('max', null, ComponentPropertyType::TYPE_NUMERIC));
        $this->addParam(new ComponentProperty('min', null, ComponentPropertyType::TYPE_NUMERIC));
        $this->addParam(new ComponentProperty('readonly', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('required', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('disabled', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('size-class', null, ComponentPropertyType::TYPE_STRING));
    }
}
