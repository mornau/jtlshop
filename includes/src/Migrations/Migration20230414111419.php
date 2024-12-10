<?php

/**
 * Change cConf of redirect_save_404 to Y
 *
 * @author sl
 * @created Fri, 14 Apr 2023 11:14:19 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230414111419
 */
class Migration20230414111419 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'Change cConf of redirect_save_404 to Y';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $settingConf = (object)['cConf' => 'Y'];
        $this->getDB()->updateRow('teinstellungenconf', 'cWertName', 'redirect_save_404', $settingConf);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $settingConf = (object)['cConf' => 'N'];
        $this->getDB()->updateRow('teinstellungenconf', 'cWertName', 'redirect_save_404', $settingConf);
    }
}
