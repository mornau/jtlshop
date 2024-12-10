<?php

/**
 * remove_unused_configgroup_5_product_tagging_config
 *
 * @author je
 * @created Tue, 05 Jan 2021 09:37:53 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210105093753
 */
class Migration20210105093753 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'je';
    }

    public function getDescription(): string
    {
        return 'Remove unused configgroup_5_product_tagging config';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "DELETE FROM `teinstellungenconf`
                WHERE kEinstellungenSektion = 5
                AND cWertName = 'configgroup_5_product_tagging'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO `teinstellungenconf`
                VALUES (626,5,'Produkttagging','','configgroup_5_product_tagging',NULL,'',1000,1,0,'N')"
        );
    }
}
