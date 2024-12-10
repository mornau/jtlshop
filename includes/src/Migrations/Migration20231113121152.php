<?php

/**
 * remove config for ckeditor startup mode
 *
 * @author dr
 * @created Mon, 13 Nov 2023 12:11:52 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20231113121152
 */
class Migration20231113121152 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'remove config for ckeditor startup mode';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('admin_ckeditor_mode');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'admin_ckeditor_mode',
            'N',
            \CONF_GLOBAL,
            'CKEditor-Modus',
            'selectbox',
            1501,
            (object)[
                'inputOptions' => [
                    'Q' => 'Quellcode',
                    'N' => 'Normal',
                ]
            ],
        );
    }
}
