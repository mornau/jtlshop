<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Router\Controller\Backend\BoxController;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200514090200
 */
class Migration20200514090200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Remove box visibilites of invalid/deprecated page types';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $pageTypes = \implode(',', BoxController::getValidPageTypes());
        $this->execute('DELETE FROM tboxensichtbar WHERE kSeite NOT IN (' . $pageTypes . ')');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
