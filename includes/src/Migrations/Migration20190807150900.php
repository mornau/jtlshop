<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Add default value for topcblueprint.kPlugin
 * Remove List Portlet
 *
 * @author dr
 */
class Migration20190807150900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Remove List Portlet';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("DELETE FROM topcportlet WHERE cClass = 'ListPortlet'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO topcportlet (kPlugin, cTitle, cClass, cGroup, bActive)
              VALUES (0, 'List', 'ListPortlet', 'layout', 1)"
        );
    }
}
