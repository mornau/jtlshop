<?php

/**
 * adds aria language section and variables
 *
 * @author ms
 * @created Thu, 15 Nov 2018 11:55:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181115115500
 */
class Migration20181115115500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add aria language section and variables';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('INSERT INTO tsprachsektion (cName) VALUES ("aria");');

        $this->setLocalization('ger', 'aria', 'primary', 'Kontext: hauptsächlich');
        $this->setLocalization('eng', 'aria', 'primary', 'primary context');

        $this->setLocalization('ger', 'aria', 'secondary', 'Kontext: nebensächlich');
        $this->setLocalization('eng', 'aria', 'secondary', 'secondary context');

        $this->setLocalization('ger', 'aria', 'success', 'Kontext: Erfolg');
        $this->setLocalization('eng', 'aria', 'success', 'success context');

        $this->setLocalization('ger', 'aria', 'danger', 'Kontext: Achtung');
        $this->setLocalization('eng', 'aria', 'danger', 'danger context');

        $this->setLocalization('ger', 'aria', 'warning', 'Kontext: Warnung');
        $this->setLocalization('eng', 'aria', 'warning', 'warning context');

        $this->setLocalization('ger', 'aria', 'info', 'Kontext: Information');
        $this->setLocalization('eng', 'aria', 'info', 'information context');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('primary');
        $this->removeLocalization('secondary');
        $this->removeLocalization('success');
        $this->removeLocalization('danger');
        $this->removeLocalization('warning');
        $this->removeLocalization('info');
        $this->execute('DELETE FROM tsprachsektion WHERE cName = "aria";');
    }
}
