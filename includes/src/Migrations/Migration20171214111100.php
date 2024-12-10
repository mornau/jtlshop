<?php

/**
 * @author fm
 * @created Thu, 11 Dec 2017 11:11:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20171214111100
 */
class Migration20171214111100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add cookie config notice';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf
                SET cName = 'Cookie-Einstellungen (Achtung: nur ändern, wenn Sie genau wissen, was Sie tun!)'
                WHERE cName = 'Cookie-Einstellungen'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf
                SET cName = 'Cookie-Einstellungen'
                WHERE cName = 'Cookie-Einstellungen (Achtung: nur ändern, wenn Sie genau wissen, was Sie tun!)'"
        );
    }
}
