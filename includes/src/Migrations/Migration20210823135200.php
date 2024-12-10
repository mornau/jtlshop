<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210823135200
 */
class Migration20210823135200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add AdminName to log table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE teinstellungenlog
                ADD COLUMN cAdminname VARCHAR(255) NOT NULL DEFAULT '' AFTER kAdminlogin"
        );
        /** @noinspection SqlWithoutWhere */
        $this->execute(
            "UPDATE teinstellungenlog SET cAdminname = COALESCE(
                (SELECT cName FROM tadminlogin WHERE tadminlogin.kAdminlogin = teinstellungenlog.kAdminlogin),
                'unknown'
            )"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE teinstellungenlog
                DROP COLUMN cAdminname'
        );
    }
}
