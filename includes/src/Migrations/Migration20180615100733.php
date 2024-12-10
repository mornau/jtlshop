<?php

/**
 * translate validUntil english
 *
 * @author mh
 * @created Fri, 15 Jun 2018 10:07:33 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180615100733
 */
class Migration20180615100733 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Translate validUntil global english';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('eng', 'global', 'validUntil', 'valid until');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
