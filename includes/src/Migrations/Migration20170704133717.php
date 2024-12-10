<?php

/**
 * Add cUserAgent to tBesucher
 *
 * @author fp
 * @created Tue, 04 Jul 2017 13:37:17 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170704133717
 */
class Migration20170704133717 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add cUserAgent to tBesucher';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tbesucher ADD COLUMN cUserAgent VARCHAR(512) NULL AFTER cReferer');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tbesucher DROP COLUMN cUserAgent');
    }
}
