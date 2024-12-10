<?php

/**
 * associate UstId-settings
 *
 * @author cr
 * @created Tue, 05 Feb 2019 14:59:48 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**+
 * Class Migration20190205145948
 */
class Migration20190205145948 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Associate UstId-settings';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('UPDATE teinstellungenconf SET nSort = 415 WHERE kEinstellungenConf = 6');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('UPDATE teinstellungenconf SET nSort = 140 WHERE kEinstellungenConf = 6');
    }
}
