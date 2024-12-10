<?php

/**
 * add dLastLogin and lastLoginHash to tkunde
 *
 * @author sl
 * @created Wed, 07 Dec 2022 16:59:51 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\DBManager;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20221207165951
 */
class Migration20221207165951 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'add dLastLogin to tkunde';
    }

    public function up(): void
    {
        $table = 'tkunde';
        if (!\array_key_exists('dLastLogin', DBManager::getColumns($table))) {
            $this->execute(
                'ALTER TABLE ' . $table .
                ' ADD COLUMN dLastLogin DATETIME DEFAULT NULL AFTER nLoginversuche'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('tkunde', 'dLastLogin');
    }
}
