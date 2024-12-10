<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240612115406
 */
class Migration20240612115406 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return /** @lang text */ 'Create table for deleted customer sync';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE deleted_customers (
                id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                kKunde      INT UNSIGNED    NOT NULL,
                customer_id VARCHAR(40)     NOT NULL,
                deleted     DATETIME        NOT NULL,
                issuer      VARCHAR(40)     NOT NULL,
                ack         INT             NOT NULL DEFAULT 0,
                PRIMARY KEY (id),
                UNIQUE KEY  idx_customer_uq (kKunde, customer_id),
                INDEX       idx_ack (ack)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE deleted_customers');
    }
}
