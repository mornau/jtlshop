<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * add_lang_var_config
 *
 * @author ms
 * @created Fri, 22 Mar 2019 13:51:00 +0100
 */

/**
 * Class Migration20190322135100
 */
class Migration20190322135100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add lang var config';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'productDetails',
            'configChooseOneComponent',
            'Bitte wählen Sie genau eine Komponente'
        );
        $this->setLocalization('eng', 'productDetails', 'configChooseOneComponent', 'Choose one component please');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('configChooseOneComponent');
    }
}
