<?php

/**
 * remove eos payment method
 *
 * @author fm
 * @created Fri, 22 Jun 2018 12:29:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180622122900
 */
class Migration20180622122900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove EOS payment method';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("DELETE FROM teinstellungen WHERE cModulId LIKE 'za_eos_%'");
        $this->execute("DELETE FROM teinstellungenconf WHERE cModulId LIKE 'za_eos_%'");
        $this->execute("DELETE FROM tzahlungsart WHERE cModulId LIKE 'za_eos_%'");
        $this->execute("DELETE FROM tsprachwerte WHERE bSystem = 1 AND cName LIKE 'eos%'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
