<?php

/**
 * Add lang var note
 *
 * @author mh
 * @created Wed, 12 Aug 2020 14:53:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200812145300
 *
 */
class Migration20200812145300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang var note';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'yourNote', 'Ihre Notiz');
        $this->setLocalization('eng', 'global', 'yourNote', 'Your note');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('yourNote');
    }
}
