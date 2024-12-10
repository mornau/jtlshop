<?php

/**
 * New index for download history
 *
 * @author fp
 * @created Mon, 13 Dec 2021 12:30:35 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20211213123035
 */
class Migration20211213123035 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'New index for download history';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tdownloadhistory ADD INDEX idx_download (kDownload)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tdownloadhistory DROP INDEX idx_download');
    }
}
