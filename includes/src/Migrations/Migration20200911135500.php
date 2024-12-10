<?php

/**
 * Create status table for or-filtered attributes
 *
 * @author fp
 * @created Fri, 11 Sep 2020 13:55:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200911135500
 */
class Migration20200911135500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create new index for similiar products';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tartikel ADD INDEX kVaterArtikel_UQ2 (nIstVater, kVaterArtikel, kArtikel)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tartikel DROP INDEX kVaterArtikel_UQ2');
    }
}
