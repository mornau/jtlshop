<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200909150300
 */
class Migration20200909150300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add license widget';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO `tadminwidgets` (`kPlugin`, `cTitle`, `cClass`, 
             `eContainer`, `cDescription`, `nPos`, `bExpanded`, `bActive`) 
             VALUES ('0', 'Lizenzen', 'LicensedItemUpdates', 'center', 'Zeigt Lizenznformationen an', '0', '1', '1')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tadminwidgets` WHERE kPlugin = 0 AND cClass = 'LicensedItemUpdates'");
    }
}
