<?php

/**
 * Refactor tstoreauth
 *
 * @author fp
 * @created Mon, 23 Mar 2020 15:56:13 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Migration
 */
class Migration20200323155613 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Refactor tstoreauth';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE tstoreauth
                ADD owner       INT             NOT NULL FIRST,
                ADD verified    VARCHAR(128)    NOT NULL,
                ADD CONSTRAINT tstoreauth_pk PRIMARY KEY (owner)'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE tstoreauth DROP owner'
        );
        $this->execute(
            'ALTER TABLE tstoreauth DROP verified'
        );
    }
}
