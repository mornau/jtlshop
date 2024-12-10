<?php

/**
 * correct_nsort_setting
 *
 * @author mh
 * @created Wed, 08 May 2019 15:46:34 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190508154634
 */
class Migration20190508154634 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Correct nsort of artikel_lagerampel_keinlager';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE teinstellungenconf SET nSort=505 WHERE cWertName = 'artikel_lagerampel_keinlager'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE teinstellungenconf SET nSort=500 WHERE cWertName = 'artikel_lagerampel_keinlager'");
    }
}
