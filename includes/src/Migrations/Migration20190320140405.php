<?php

/**
 *
 *
 * @author mh
 * @created Wed, 20 Mar 2019 14:04:05 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190320140405
 */
class Migration20190320140405 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add missing continents';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE tland
                SET cKontinent = 'Nordamerika'
                WHERE cISO
                  IN ('AG', 'AI', 'AW', 'BB', 'BS', 'CU', 'DM', 'DO', 'GD', 'GP', 'HT',
                      'KN', 'KY', 'LC', 'MQ', 'MS', 'PR', 'TC', 'TT', 'VC', 'JM')"
        );
        $this->execute(
            "UPDATE tland
                SET cKontinent = 'Antarktis'
                WHERE cISO
                  IN ('BV', 'HM')"
        );
        $this->execute(
            "UPDATE tland
                SET cKontinent = 'Asien'
                WHERE cISO
                  IN ('CC', 'CX', 'MO', 'UZ', 'KZ')"
        );
        $this->execute(
            "UPDATE tland
                SET cKontinent = 'Afrika'
                WHERE cISO
                  IN ('RE', 'SH', 'YT', 'EG')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
