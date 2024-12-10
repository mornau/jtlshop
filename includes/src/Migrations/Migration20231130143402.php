<?php

/**
 * Create table for shipping method wizard
 *
 * @author fp
 * @created Thu, 30 Nov 2023 14:34:02 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20231130143402
 */
class Migration20231130143402 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create tables for shipping class wizard';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE `shipping_class_wizard` (
                `id`             INT         NOT NULL AUTO_INCREMENT,
                `kVersandart`    INT         NOT NULL,
                `definition`     MEDIUMTEXT  NOT NULL,
                `result_hash`    VARCHAR(40) NOT NULL DEFAULT \'\',
                PRIMARY KEY (`id`),
                UNIQUE KEY idx_shipment_uq (`kVersandart`)
            ) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'DROP TABLE IF EXISTS `shipping_class_wizard`'
        );
    }
}
