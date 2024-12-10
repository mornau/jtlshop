<?php

/**
 * Change kSprache column to store an IETF language tag
 *
 * @author dr
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190319103100
 */
class Migration20190319103100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Change kSprache column to store an IETF language tag';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tadminlogin DROP COLUMN kSprache');
        $this->execute("ALTER TABLE tadminlogin ADD COLUMN language VARCHAR(35) DEFAULT 'de-DE'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $stdLang = $this->getDB()->getSingleInt(
            'SELECT kSprache
                FROM tsprache
                WHERE cShopStandard = \'Y\'',
            'kSprache'
        );
        $this->execute('ALTER TABLE tadminlogin ADD COLUMN kSprache TINYINT(3) UNSIGNED DEFAULT ' . $stdLang);
        $this->execute('ALTER TABLE tadminlogin DROP COLUMN language');
    }
}
