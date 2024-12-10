<?php

/**
 * Move extension viewer widget to status.php
 *
 * @author mh
 * @created Tue, 01 Sep 2020 12:32:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200901123200
 */
class Migration20200901123200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Move extension viewer widget to status.php';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("DELETE FROM `tadminwidgets` WHERE cClass='ExtensionViewer'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO tadminwidgets (
                    kPlugin, cTitle, cClass, eContainer, cDescription, nPos, bExpanded, bActive
                )
                VALUES (0, 'Erweiterungen', 'ExtensionViewer', 'center', 'Zeigt alle aktiven Erweiterungen', 4, 1, 1)"
        );
    }
}
