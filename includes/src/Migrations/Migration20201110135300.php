<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201110135300
 */
class Migration20201110135300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove availability sorting option';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'DELETE FROM `teinstellungenconfwerte` WHERE kEinstellungenConf = 190 AND cWert = 8'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO `teinstellungenconfwerte` (kEinstellungenConf, cName, cWert, nSort) 
                VALUES (190, 'Verfügbarkeit', 8, 8)"
        );
    }
}
