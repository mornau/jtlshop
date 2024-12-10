<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210323112600
 */
class Migration20210323112600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add line ending config for exports';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'exportformate_line_ending',
            'N',
            \CONF_EXPORTFORMATE,
            'Line ending',
            'selectbox',
            170,
            (object)[
                'cBeschreibung' => 'Line ending',
                'inputOptions'  => [
                    'LF'   => 'LF',
                    'CRLF' => 'CRLF',
                ],
            ]
        );
        $this->execute('ALTER TABLE `texportformat` ADD COLUMN `async` TINYINT(1) NULL DEFAULT 0');
        $this->execute('UPDATE texportformat SET async = 1 WHERE kPlugin = 0');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('exportformate_line_ending');
        $this->execute('ALTER TABLE `texportformat` DROP COLUMN `async`');
    }
}
