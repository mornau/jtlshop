<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210803124200
 */
class Migration20210803124200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add wishlist invisible item lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'wishlist',
            'warningInvisibleItems',
            '%s Artikel sind derzeit nicht verfügbar '
            . 'und werden deshalb nicht angezeigt.'
        );
        $this->setLocalization(
            'eng',
            'wishlist',
            'warningInvisibleItems',
            '%s items are invisbile because they are '
            . 'not available.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('warningInvisibleItems', 'wishlist');
    }
}
