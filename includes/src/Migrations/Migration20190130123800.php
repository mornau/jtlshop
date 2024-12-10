<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Change List Portlet class name
 *
 * @author dr
 */
class Migration20190130123800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Change List Portlet class name';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE topcportlet SET cClass = 'ListPortlet' WHERE cClass = 'PList'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE topcportlet SET cClass = 'PList' WHERE cClass = 'ListPortlet'");
    }
}
