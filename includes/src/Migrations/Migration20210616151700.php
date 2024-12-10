<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210616151700
 */
class Migration20210616151700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Consent support for templates';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `tconsent` 
                ADD COLUMN `templateID` VARCHAR(255) NULL DEFAULT NULL'
        );
        $this->execute(
            'ALTER TABLE `tglobals` 
                ADD COLUMN `consentVersion` INT NOT NULL DEFAULT 1'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE `tconsent` 
                DROP COLUMN `templateID`'
        );
        $this->execute(
            'ALTER TABLE `tglobals` 
                DROP COLUMN `consentVersion`'
        );
    }
}
