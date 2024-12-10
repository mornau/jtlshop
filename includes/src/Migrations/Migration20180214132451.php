<?php

/**
 * Increase migration content length
 *
 * @author mschop
 * @created Wed, 14 Feb 2018 13:24:51 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180214132451
 */
class Migration20180214132451 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mschop';
    }

    public function getDescription(): string
    {
        return 'Increase revisions content length';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE trevisions MODIFY content LONGTEXT');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE trevisions MODIFY content TEXT');
    }
}
