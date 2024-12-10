<?php

/**
 * @author ms
 * @created Thu, 14 May 2020 14:35:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200514143500
 */
class Migration20200514143500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Adds lang var for privacy notice';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'privacyNotice', 'Bitte beachten Sie unsere Datenschutzerklärung');
        $this->setLocalization('eng', 'global', 'privacyNotice', 'Please see our privacy notice');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('privacyNotice', 'global');
    }
}
