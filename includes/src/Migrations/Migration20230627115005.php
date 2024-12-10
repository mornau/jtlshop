<?php

/**
 * Uninstall jtl widgets if installed
 *
 * @author sl
 * @created Tue, 27 Jun 2023 11:50:05 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Plugin\Helper;
use JTL\Plugin\State;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230627115005
 */
class Migration20230627115005 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'deactivate jtl widgets plugin if installed';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        Helper::getPluginById('jtl_widgets')?->selfDestruct(State::DISABLED, $this->getDB());
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
