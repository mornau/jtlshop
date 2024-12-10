<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181102102400
 */
class Migration20181102102400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Change OPC page id type';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE topcpage DROP INDEX cPageId');
        $this->execute('ALTER TABLE topcpage MODIFY cPageId MEDIUMTEXT NOT NULL');
        $this->execute('ALTER TABLE topcpage ADD UNIQUE INDEX (cPageId(255), dPublishFrom)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE topcpage DROP INDEX cPageId');
        $this->execute('ALTER TABLE topcpage MODIFY cPageId CHAR(32) NOT NULL');
        $this->execute('ALTER TABLE topcpage ADD UNIQUE INDEX (cPageId, dPublishFrom)');
    }
}
