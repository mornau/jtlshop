<?php

/**
 * add_global_lang_or
 *
 * @author mh
 * @created Wed, 20 Feb 2019 11:50:10 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190220115010
 */
class Migration20190220115010 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add global lang var or';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'or', 'oder');
        $this->setLocalization('eng', 'global', 'or', 'or');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('or');
    }
}
