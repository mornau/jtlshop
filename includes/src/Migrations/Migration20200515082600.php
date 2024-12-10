<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Shop;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200515082600
 */
class Migration20200515082600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Readjust slider image paths';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $mediafilesPath = \PFAD_MEDIAFILES;

        $this->execute(
            "UPDATE tslide
                SET cBild = CONCAT('" . $mediafilesPath . "', cBild),
                    cThumbnail = CONCAT('" . $mediafilesPath . "', 'Bilder/.tmb/', substring_index(cBild, '/', -1))
                WHERE cBild LIKE 'Bilder/%'"
        );

        $shopPath = \parse_url(Shop::getURL() . '/', PHP_URL_PATH);

        $this->execute(
            "UPDATE tslide
                SET cBild = TRIM(LEADING '" . $shopPath . "' FROM cBild)
                WHERE cBild LIKE '" . $shopPath . "%'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
