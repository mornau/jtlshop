<?php

/**
 * Add 'google two-factor-authentication'
 * Issue #276
 *
 * @author root
 * @created Wed, 27 Jul 2016 14:14:07 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160727141407
 */
class Migration20160727141407 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE tadminlogin
                ADD b2FAauth tinyint(1) DEFAULT 0,
                ADD c2FAauthSecret VARCHAR(100) DEFAULT '';"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('tadminlogin', 'b2FAauth');
        $this->dropColumn('tadminlogin', 'c2FAauthSecret');
    }
}
