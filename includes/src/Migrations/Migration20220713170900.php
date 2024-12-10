<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220713170900
 */
class Migration20220713170900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Misc language fixes';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'login', 'wishlistRename', 'Wunschzettel umbenennen');
        $this->setLocalization('ger', 'account data', 'noOrdersYet', 'Sie haben noch keine Bestellung aufgegeben.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
