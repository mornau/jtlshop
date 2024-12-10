<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20221118111100
 */
class Migration20221118111100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Rename consent reject button lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'consent', 'close', 'Ablehnen');
        $this->setLocalization('eng', 'consent', 'close', 'Reject');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'consent', 'close', 'Schließen');
        $this->setLocalization('eng', 'consent', 'close', 'Close');
    }
}
