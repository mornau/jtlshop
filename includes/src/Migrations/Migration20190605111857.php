<?php

/**
 * Remove table tnewsletterqueue
 *
 * @author cr
 * @created Wed, 05 Jun 2019 11:18:57 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190605111857
 */
class Migration20190605111857 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Remove table tnewsletterqueue';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('DROP TABLE tnewsletterqueue');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'CREATE TABLE tnewsletterqueue (
                kNewsletterQueue int(10) unsigned NOT NULL AUTO_INCREMENT,
                kNewsletter int(10) unsigned NOT NULL,
                nAnzahlEmpfaenger int(10) unsigned NOT NULL,
                dStart datetime NOT NULL,
            PRIMARY KEY (kNewsletterQueue),
            KEY kNewsletter (kNewsletter)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }
}
