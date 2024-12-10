<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240626124918
 */
class Migration20240626124918 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'add ManufacturerSlider portlet';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO topcportlet (kPlugin, cTitle, cClass, cGroup, bActive)
            VALUES (0, 'Manufacturer Slider', 'ManufacturerSlider', 'content', 1)"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM topcportlet WHERE cClass = 'ManufacturerSlider'");
    }
}
