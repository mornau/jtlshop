<?php

/**
 * global_meta_title_anhaengen setting title
 *
 * @author ms
 * @created Tue, 17 Jan 2017 16:19:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160205105322
 */
class Migration20170117161900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf 
                SET cName = 'Meta Title an Produktseiten anhängen'
                WHERE kEinstellungenConf = '140';"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf
                SET cName = 'Meta Title überall anhängen'
                WHERE kEinstellungenConf = '140';"
        );
    }
}
