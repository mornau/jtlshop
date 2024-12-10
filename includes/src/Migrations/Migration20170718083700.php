<?php

/**
 * corrects email template name
 *
 * @author ms
 * @created Tue, 18 Jul 2017 08:37:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170718083700
 */
class Migration20170718083700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Correct email template name';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE temailvorlage
                SET cName = 'Warenrücksendung abgeschickt'
                WHERE cModulId = 'core_jtl_rma_submitted' AND cDateiname = 'rma'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE temailvorlage
                SET cName = 'Warenrücksendung abegeschickt'
                WHERE cModulId = 'core_jtl_rma_submitted' AND cDateiname = 'rma'"
        );
    }
}
