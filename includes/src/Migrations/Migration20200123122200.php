<?php

/**
 * Remove did you know widget
 *
 * @author mh
 * @created Thu, 23 Jan 2020 12:23:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200123122200
 */
class Migration20200123122200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove did you know widget';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("DELETE FROM `tadminwidgets` WHERE cClass='Duk'");
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
                VALUES (0, 'Wussten Sie schon', 'Duk', 'left', 'Nützliche Tipps zu JTL-Shop', 3, 1, 1)"
        );
    }
}
