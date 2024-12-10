<?php

/**
 * Add index on tnewsletterempfaenger.kKunde
 *
 * @author fp
 * @created Thu, 22 Dec 2016 13:50:18 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161222135018
 */
class Migration20161222135018 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add index on tnewsletterempfaenger.kKunde';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tnewsletterempfaenger ADD INDEX kKunde (kKunde)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tnewsletterempfaenger DROP INDEX kKunde');
    }
}
