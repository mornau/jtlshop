<?php

/**
 * adds poll error message
 *
 * @author ms
 * @created Mon, 19 Dec 2016 13:00:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161219130000
 */
class Migration20161219130000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'messages', 'pollError', 'Bei der Auswertung ist ein Fehler aufgetreten.');
        $this->setLocalization('eng', 'messages', 'pollError', 'An error occured during validation.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('pollError');
    }
}
