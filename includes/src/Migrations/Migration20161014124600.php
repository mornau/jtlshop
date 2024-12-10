<?php

/**
 * add language variables for news overview meta description
 *
 * @author ms
 * @created Fri, 14 Oct 2016 12:46:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161014124600
 */
class Migration20161014124600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'news',
            'newsMetaDesc',
            'Neuigkeiten und Aktuelles zu unserem Sortiment und unserem Onlineshop'
        );
        $this->setLocalization('eng', 'news', 'newsMetaDesc', 'News and updates to our range and our online shop');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('newsMetaDesc');
    }
}
