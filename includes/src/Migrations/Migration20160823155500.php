<?php

/**
 * WRB
 *
 * @author fm
 * @created Tue, 23 Aug 2016 15:55:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160823155500
 */
class Migration20160823155500 extends Migration implements IMigration
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
                ADD COLUMN cWRBFormContentHtml TEXT DEFAULT '',
                ADD COLUMN cWRBFormContentText TEXT DEFAULT ''"
        );
        $this->execute(
            'ALTER TABLE temailvorlage 
                ADD COLUMN nWRBForm TINYINT(3) UNSIGNED NOT NULL DEFAULT 0'
        );
        $this->execute(
            'ALTER TABLE tpluginemailvorlage 
                ADD COLUMN nWRBForm TINYINT(3) UNSIGNED NOT NULL DEFAULT 0'
        );
        $this->execute(
            'ALTER TABLE temailvorlageoriginal 
                ADD COLUMN nWRBForm TINYINT(3) UNSIGNED NOT NULL DEFAULT 0'
        );
        $this->setLocalization('ger', 'global', 'wrbform', 'Muster-Widerrufsbelehrungsformular');
        $this->setLocalization('eng', 'global', 'wrbform', 'Model withdrawal form');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('ttext', 'cWRBFormContentHtml');
        $this->dropColumn('ttext', 'cWRBFormContentText');
        $this->dropColumn('temailvorlage', 'nWRBForm');
        $this->dropColumn('tpluginemailvorlage', 'nWRBForm');
        $this->dropColumn('temailvorlageoriginal', 'nWRBForm');
        $this->execute("DELETE FROM tsprachwerte WHERE cName = 'wrbform' AND kSprachsektion = 1");
    }
}
