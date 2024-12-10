<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200204112200
 */
class Migration20200204112200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove global meta keywords';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('DROP TABLE texcludekeywords');
        $this->execute("DELETE FROM tglobalemetaangaben WHERE cName = 'Meta_Keywords'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'CREATE TABLE `texcludekeywords` (
                  `cISOSprache` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `cKeywords` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
        );
        $this->execute(
            "INSERT INTO `texcludekeywords` 
            VALUES ('ger','aus ohne mit der die das zur für in einer eine einem sein seine'),
                   ('eng','with without it in out')"
        );
    }
}
