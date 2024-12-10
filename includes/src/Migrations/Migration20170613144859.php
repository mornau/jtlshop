<?php

/**
 * Defaults for new template settings in productlist
 *
 * @author fp
 * @created Tue, 13 Jun 2017 14:48:59 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Shop;
use JTL\Template\Config;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170613144859
 */
class Migration20170613144859 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Defaults for new template settings in productlist';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $template = Shop::Container()->getTemplateService()->getActiveTemplate(false);
        $config   = new Config($template->getDir(), $this->getDB());
        $settings = Shop::getSettings([\CONF_TEMPLATE])['template'];
        if ($template->getName() === 'Evo' || $template->getParent() === 'Evo') {
            if (!isset($settings['productlist']['variation_select_productlist'])) {
                $config->updateConfigInDB('productlist', 'variation_select_productlist', 'N');
            }
            if (!isset($settings['productlist']['variation_select_productlist'])) {
                $config->updateConfigInDB('productlist', 'quickview_productlist', 'N');
            }
            if (!isset($settings['productlist']['variation_select_productlist'])) {
                $config->updateConfigInDB('productlist', 'hover_productlist', 'N');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $currentTemplate = Shop::Container()->getTemplateService()->getActiveTemplate(false);
        $this->execute(
            "DELETE FROM ttemplateeinstellungen
            WHERE cTemplate = '" . $currentTemplate->getDir() . "' AND cSektion = 'productlist'"
        );
    }
}
