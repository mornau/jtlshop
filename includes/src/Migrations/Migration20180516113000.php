<?php

/**
 * DSE
 *
 * @author fm
 * @created Wed, 16 May 2018 11:30:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180516113000
 */
class Migration20180516113000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE ttext 
                ADD COLUMN cDSEContentText TEXT DEFAULT '',
                ADD COLUMN cDSEContentHtml TEXT DEFAULT ''"
        );
        $this->execute(
            'ALTER TABLE temailvorlage 
                ADD COLUMN nDSE TINYINT(3) UNSIGNED NOT NULL DEFAULT 0'
        );
        $this->execute(
            'ALTER TABLE tpluginemailvorlage 
                ADD COLUMN nDSE TINYINT(3) UNSIGNED NOT NULL DEFAULT 0'
        );
        $this->execute(
            'ALTER TABLE temailvorlageoriginal 
                ADD COLUMN nDSE TINYINT(3) UNSIGNED NOT NULL DEFAULT 0'
        );
        $this->setLocalization('ger', 'global', 'dse', 'Datenschutzerklärung');
        $this->setLocalization('eng', 'global', 'dse', 'Data privacy policy');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('ttext', 'cDSEContentText');
        $this->dropColumn('ttext', 'cDSEContentHtml');
        $this->dropColumn('temailvorlage', 'nDSE');
        $this->dropColumn('tpluginemailvorlage', 'nDSE');
        $this->dropColumn('temailvorlageoriginal', 'nDSE');
        $this->execute("DELETE FROM tsprachwerte WHERE cName = 'dse' AND kSprachsektion = 1");
    }
}
