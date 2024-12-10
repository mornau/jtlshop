<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240628120646
 */
class Migration20240628120646 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'sets system language variables bSystem';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'UPDATE tsprachwerte
                SET bSystem = 1
                WHERE cName = "financingIncludesProcessingFee"
                    OR cName = "matches"
                    OR cName = "merchandiseValue"
                    OR cName = "noMediaFile"
                    OR cName = "pricePerUnit"
                    OR cName = "quantity"'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'UPDATE tsprachwerte
                SET bSystem = 0
                WHERE cName = "financingIncludesProcessingFee"
                    OR cName = "matches"
                    OR cName = "merchandiseValue"
                    OR cName = "noMediaFile"
                    OR cName = "pricePerUnit"
                    OR cName = "quantity"'
        );
    }
}
