<?php

declare(strict_types=1);

namespace JTL\Plugin\Admin\Installation\Items;

use JTL\Plugin\InstallCode;
use stdClass;

/**
 * Class Templates
 * @package JTL\Plugin\Admin\Installation\Items
 */
class Templates extends AbstractItem
{
    /**
     * @inheritdoc
     */
    public function getNode(): array
    {
        return (array)($this->baseNode['Install'][0]['ExtendedTemplates'][0]['Template'] ?? []);
    }

    /**
     * @inheritdoc
     */
    public function install(): int
    {
        foreach ($this->getNode() as $template) {
            \preg_match('/[a-zA-Z\d\/_\-]+\.tpl/', $template, $hits);
            if (\mb_strlen($hits[0]) !== \mb_strlen($template)) {
                continue;
            }
            $plgnTpl            = new stdClass();
            $plgnTpl->kPlugin   = $this->getPlugin()->kPlugin;
            $plgnTpl->cTemplate = $template;
            if (!$this->getDB()->insert('tplugintemplate', $plgnTpl)) {
                return InstallCode::SQL_CANNOT_SAVE_TEMPLATE;
            }
        }

        return InstallCode::OK;
    }
}
