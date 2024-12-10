<?php declare(strict_types=1);

namespace scc;

use scc\exceptions\ComponentAlreadyRegisteredException;

/**
 * Class Renderer
 * @package scc
 */
class Renderer implements RendererInterface
{
    /**
     * @var \JTL\Smarty\JTLSmarty|\Smarty
     */
    protected $smarty;

    protected const TEMPLATES_DIR = __DIR__ . '/' . 'templates';

    /**
     * Renderer constructor.
     * @param \JTL\Smarty\JTLSmarty|\Smarty $smarty
     */
    public function __construct($smarty)
    {
        $this->smarty = $smarty;
        $this->smarty->addTemplateDir(self::TEMPLATES_DIR, __NAMESPACE__);
    }

    /**
     * @inheritdoc
     */
    public function registerComponent(ComponentInterface $component): void
    {
        try {
            $this->smarty->registerPlugin(
                $component->getType(),
                $component->getName(),
                [$component->getRenderer(), 'render']
            );
        } catch (\SmartyException) {
            throw new ComponentAlreadyRegisteredException(
                'The component ' . $component->getName() . ' is already registered'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function unregisterComponent(string $type, string $name): void
    {
        $this->smarty->unregisterPlugin($type, $name);
    }

    /**
     * @inheritdoc
     */
    public function getRegisteredComponents(string $type): array
    {
        return $this->smarty->registered_plugins[$type];
    }
}
