<?php

/**
 * change wrong language-variable-values
 *
 * @author cr
 * @created Tue, 06 Mar 2018 16:09:13 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180306160913
 */
class Migration20180306160913 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'global',
            'uploadInvalidFormat',
            'Die Datei entspricht nicht dem geforderten Format'
        );
        $this->setLocalization('ger', 'global', 'paginationOrderUsefulness', 'Hilfreich');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization(
            'ger',
            'global',
            'uploadInvalidFormat',
            'Die Datei entspricht nicht dem geforderte Format'
        );
        $this->setLocalization('ger', 'global', 'paginationOrderUsefulness', 'Hilreich');
    }
}
