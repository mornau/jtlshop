<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Helpers\Text;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20231026154000
 */
class Migration20231026154000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Image size selection for branding';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $default = Text::createSSK(['xl', 'lg']);
        $this->execute(
            'ALTER TABLE `tbrandingeinstellung` 
                ADD COLUMN `imagesizes` VARCHAR(255) NOT NULL DEFAULT \'' . $default . '\''
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE `tbrandingeinstellung` 
                DROP COLUMN `imagesizes`'
        );
    }
}
