<?php

declare(strict_types=1);

namespace JTL\Plugin\Admin\Installation\Items;

use JTL\Plugin\InstallCode;
use stdClass;

/**
 * Class Checkboxes
 * @package JTL\Plugin\Admin\Installation\Items
 */
class Checkboxes extends AbstractItem
{
    /**
     * @inheritdoc
     */
    public function getNode(): array
    {
        return isset($this->baseNode['Install'][0]['CheckBoxFunction'][0]['Function'])
        && \is_array($this->baseNode['Install'][0]['CheckBoxFunction'][0]['Function'])
        && \count($this->baseNode['Install'][0]['CheckBoxFunction'][0]['Function']) > 0
            ? $this->baseNode['Install'][0]['CheckBoxFunction'][0]['Function']
            : [];
    }

    /**
     * @inheritdoc
     */
    public function install(): int
    {
        foreach ($this->getNode() as $i => $function) {
            $i = (string)$i;
            \preg_match('/\d+/', $i, $hits);
            if (\mb_strlen($hits[0]) !== \mb_strlen($i)) {
                continue;
            }
            $cbFunction          = new stdClass();
            $cbFunction->kPlugin = $this->getPlugin()->kPlugin;
            $cbFunction->cName   = $function['Name'];
            $cbFunction->cID     = $this->getPlugin()->cPluginID . '_' . $function['ID'];
            $this->getDB()->insert('tcheckboxfunktion', $cbFunction);
        }

        return InstallCode::OK;
    }
}
