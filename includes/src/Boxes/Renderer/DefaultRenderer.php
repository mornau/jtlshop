<?php

declare(strict_types=1);

namespace JTL\Boxes\Renderer;

use Exception;
use JTL\Boxes\Items\BoxInterface;
use Smarty_Internal_TemplateBase;

/**
 * Class DefaultRenderer
 * @package JTL\Boxes\Renderer
 */
class DefaultRenderer implements RendererInterface
{
    /**
     * @var Smarty_Internal_TemplateBase
     */
    protected Smarty_Internal_TemplateBase $smarty;

    /**
     * @var BoxInterface|null
     */
    protected ?BoxInterface $box;

    /**
     * @inheritdoc
     */
    public function __construct(Smarty_Internal_TemplateBase $smarty, BoxInterface $box = null)
    {
        $this->smarty = $smarty;
        $this->box    = $box;
    }

    /**
     * @inheritdoc
     */
    public function setBox(BoxInterface $box): void
    {
        $this->box = $box;
    }

    /**
     * @inheritdoc
     */
    public function getBox(): BoxInterface
    {
        if ($this->box === null) {
            throw new Exception('Box not set');
        }
        return $this->box;
    }

    /**
     * @inheritdoc
     */
    public function render(int $pageType = \PAGE_UNBEKANNT, int $pageID = 0): string
    {
        try {
            $box = $this->getBox();
            $this->smarty->assign('oBox', $box);
            $html = $box->getTemplateFile() !== '' && $box->isBoxVisible($pageType, $pageID)
                ? $this->smarty->fetch($box->getTemplateFile())
                : '';
        } catch (Exception $e) {
            $html = $e->getMessage();
        }
        $this->smarty->clearAssign('oBox');

        return $html;
    }
}
