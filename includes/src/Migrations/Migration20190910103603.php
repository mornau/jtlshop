<?php

/**
 * Remove tkonfiggruppe.nSort
 *
 * @author fp
 * @created Tue, 10 Sep 2019 10:36:03 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Migration
 */
class Migration20190910103603 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fpr';
    }

    public function getDescription(): string
    {
        return 'Remove tkonfiggruppe.nSort';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tkonfiggruppe DROP COLUMN nSort');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tkonfiggruppe ADD COLUMN nSort INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER nTyp');
    }
}
