<?php

/**
 * Adds theme setting to teinstellungen
 *
 * @author Tim Niko Tegtmeyer
 * @created Fri, 16 Jun 2023 14:32:07 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230616143207
 */
class Migration20230616143207 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'Tim Niko Tegtmeyer';
    }

    public function getDescription(): string
    {
        return 'Adds theme mode setting to tadminlogin';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("ALTER TABLE `tadminlogin` ADD theme VARCHAR(5) NOT NULL DEFAULT 'auto'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tadminlogin` DROP COLUMN theme');
    }
}
