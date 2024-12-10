<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210914080323
 */
class Migration20210914080323 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Reset fallback payment';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE tzahlungsart SET nNutzbar=0 WHERE cModulId='za_null_jtl'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
