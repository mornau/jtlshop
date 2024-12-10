<?php

/**
 * remove rma special page
 *
 * @author fm
 * @created Wed, 09 May 2018 15:21:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180509152100
 */
class Migration20180509152100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove RMA special page';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("DELETE FROM tspezialseite WHERE cDateiname = 'rma.php'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("INSERT INTO tspezialseite VALUES (23,0,'Warenrücksendung','rma.php',28,28)");
    }
}
