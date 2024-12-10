<?php

/**
 * Remove looped live search
 *
 * @author fp
 * @created Tue, 02 Nov 2021 13:31:54 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20211102133154
 */
class Migration20211102133154 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Remove infinite loop from live search';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('DELETE FROM tsuchanfragemapping WHERE cSuche = cSucheNeu');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
