<?php declare(strict_types=1);

namespace scc\renderers;

use scc\ComponentInterface;
use scc\ComponentPropertyInterface;
use scc\ComponentPropertyType;
use scc\ComponentRendererInterface;

/**
 * Class BlockRenderer
 * @package scc\renderers
 */
class BlockRenderer implements ComponentRendererInterface
{
    /**
     * BlockRenderer constructor.
     *
     * @param ComponentInterface $component
     */
    public function __construct(protected ComponentInterface $component)
    {
    }

    /**
     * @inheritdoc
     */
    public function preset(): void
    {
        foreach ($this->component->getParams() as $param) {
            if ($param->getType() === ComponentPropertyType::TYPE_UNIQUEID) {
                $param->setValue(\uniqid('', false));
            } elseif (($defaultValue = $param->getDefaultValue()) !== null) {
                $param->setValue($defaultValue);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function render(array $params, ...$args): string
    {
        [$content, $tpl] = $args;
        $params          = $this->mergeParams($params);
        if ($content === null) {
            $tpl->smarty->assign('parentBlockParams', $params)
                ->assign('pbp', $params);

            return '';
        }

        return $tpl->assign('params', $params)
            ->assign('blockContent', $content)
            ->assign('parentSmarty', $tpl->smarty)
            ->fetch($this->component->getTemplate());
    }

    /**
     * @param array $dynamic
     * @return ComponentPropertyInterface[]
     */
    protected function mergeParams(array $dynamic): array
    {
        $params = $this->component->getParams();
        $clone  = [];
        foreach ($params as $name => $param) {
            $clone[$name] = clone $param;
        }
        foreach ($dynamic as $name => $value) {
            if (isset($clone[$name])) {
                $clone[$name]->setValue($value);
            }
        }

        return $clone;
    }
}
