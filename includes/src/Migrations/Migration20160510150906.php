<?php

/**
 * add news category image row
 *
 * @author dr
 * @created Thu, 28 Apr 2016 16:27:06 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160510150906
 */
class Migration20160510150906 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tnewskategorie ADD `cPreviewImage` VARCHAR(255)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('tnewskategorie', 'cPreviewImage');
    }
}
