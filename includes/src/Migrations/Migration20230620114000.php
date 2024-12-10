<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Backend\Permissions;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230620114000
 */
class Migration20230620114000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'New plugin detail permissions';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $groups = $this->db->getInts(
            'SELECT kAdminlogingruppe FROM tadminrechtegruppe
                WHERE cRecht = :cr',
            'kAdminlogingruppe',
            ['cr' => Permissions::PLUGIN_ADMIN_VIEW]
        );
        foreach ($groups as $group) {
            $this->execute(
                "INSERT INTO tadminrechtegruppe (cRecht, kAdminlogingruppe)
                VALUES ('" . Permissions::PLUGIN_DETAIL_VIEW_ALL . "', " . $group . ')'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM tadminrechtegruppe WHERE cRecht = '" . Permissions::PLUGIN_DETAIL_VIEW_ALL . "'");
    }
}
