<?php

/**
 * Add language column to adminlogin table
 *
 * @author dr
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181130110200
 */
class Migration20181130110200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add language column to adminlogin table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $stdLang = $this->getDB()->getSingleInt(
            'SELECT kSprache
                FROM tsprache
                WHERE cShopStandard = \'Y\'',
            'kSprache'
        );
        $this->execute('ALTER TABLE tadminlogin ADD COLUMN kSprache TINYINT(3) UNSIGNED DEFAULT ' . $stdLang);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tadminlogin DROP COLUMN kSprache');
    }
}
