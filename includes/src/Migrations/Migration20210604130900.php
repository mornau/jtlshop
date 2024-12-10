<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210604130900
 */
class Migration20210604130900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Set battery law page to no system page';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('UPDATE `tlink` SET bIsSystem = 0 WHERE nLinkart = ' . LINKTYP_BATTERIEGESETZ_HINWEISE);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('UPDATE `tlink` SET bIsSystem = 1 WHERE nLinkart = ' . LINKTYP_BATTERIEGESETZ_HINWEISE);
    }
}
