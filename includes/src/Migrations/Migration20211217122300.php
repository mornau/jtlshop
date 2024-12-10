<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20211217122300
 */
class Migration20211217122300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add mini cart item overflow note';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'basket',
            'itemOverflowNotice',
            'Es befinden sich %d weitere Artikel im <a href="%s">Warenkorb</a>.'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'itemOverflowNotice',
            'There are %d more items in your <a href="%s">basket</a>.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('itemOverflowNotice', 'basket');
    }
}
