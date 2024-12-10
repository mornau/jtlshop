<?php

/**
 * add_missing_initial_data_for_some_boxes
 *
 * @author mh
 * @created Tue, 20 Nov 2018 10:41:26 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181120104126
 */
class Migration20181120104126 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add missing initial data for some boxes';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $missingBoxes = [36, 38, 40, 21, 42];

        foreach ($missingBoxes as $missingBox) {
            $this->execute(
                'INSERT IGNORE
                    INTO `tboxenanzeige` (`nSeite`, `ePosition`, `bAnzeigen`)
                    VALUES (' . $missingBox . ", 'left', 1)"
            );
            $this->execute(
                'INSERT IGNORE
                    INTO `tboxenanzeige` (`nSeite`, `ePosition`, `bAnzeigen`)
                    VALUES (' . $missingBox . ", 'bottom', 1)"
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
