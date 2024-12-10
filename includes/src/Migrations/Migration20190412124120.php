<?php

/**
 * correct_selection_wizard_permission
 *
 * @author mh
 * @created Fri, 12 Apr 2019 12:41:20 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190412124120
 */
class Migration20190412124120 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Correct selection wizard permission';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE `tadminrecht`
                SET cRecht='EXTENSION_SELECTIONWIZARD_VIEW'
                WHERE cRecht='EXTENSION_SELECTIONMWIZARD_VIEW'"
        );
        $this->execute(
            "UPDATE `tadminrechtegruppe`
                SET cRecht='EXTENSION_SELECTIONWIZARD_VIEW'
                WHERE cRecht='EXTENSION_SELECTIONMWIZARD_VIEW'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE `tadminrecht`
                SET cRecht='EXTENSION_SELECTIONMWIZARD_VIEW'
                WHERE cRecht='EXTENSION_SELECTIONWIZARD_VIEW'"
        );
        $this->execute(
            "UPDATE `tadminrechtegruppe`
                SET cRecht='EXTENSION_SELECTIONMWIZARD_VIEW'
                WHERE cRecht='EXTENSION_SELECTIONWIZARD_VIEW'"
        );
    }
}
