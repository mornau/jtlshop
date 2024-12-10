<?php

/**
 * Change default setting redirect_save_404 to No
 *
 * @author cr
 * @created Mon, 31 Jan 2022 09:44:55 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220131094455
 */
class Migration20220131094455 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Change default setting redirect_save_404 to No';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE `teinstellungen_default` SET cWert = 'N' WHERE cName = 'redirect_save_404';");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE `teinstellungen_default` SET cWert = 'Y' WHERE cName = 'redirect_save_404';");
    }
}
