<?php

/**
 * syntax checks
 *
 * @author fm
 * @created Thu, 18 Apr 2019 14:47:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200522000000
 */
class Migration20200522000000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Syntax checks';
    }

    /**
     * @inheritdoc
     * @noinspection SqlWithoutWhere
     */
    public function up(): void
    {
        // removed syntax check - set only to unchecked SHOP-4630
        $this->execute('UPDATE texportformat SET nFehlerhaft = -1');
        $this->execute('UPDATE temailvorlage SET nFehlerhaft = -1');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
