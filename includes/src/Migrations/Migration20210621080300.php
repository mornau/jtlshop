<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Shop;
use JTL\Template\Config;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210621080300
 */
class Migration20210621080300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add nova sidebar setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $template = Shop::Container()->getTemplateService()->getActiveTemplate(false);
        $config   = new Config($template->getDir(), $this->getDB());
        $settings = Shop::getSettings([\CONF_TEMPLATE])['template'];
        if (
            !isset($settings['theme']['left_sidebar'])
            && ($template->getName() === 'NOVA' || $template->getParent() === 'NOVA')
        ) {
            $config->updateConfigInDB('theme', 'left_sidebar', 'N');
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "DELETE FROM ttemplateeinstellungen
                WHERE cTemplate = 'NOVA'
                  AND cName='left_sidebar'
                  AND cSektion = 'theme'"
        );
    }
}
