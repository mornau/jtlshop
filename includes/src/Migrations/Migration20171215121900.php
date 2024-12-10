<?php

/**
 * Move language variables "invalidHash" und "invalidCustomer" to account data
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20171215121900
 */
class Migration20171215121900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fg';
    }

    public function getDescription(): string
    {
        return 'Move language variables "invalidHash" und "invalidCustomer" to account data';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->getDB()->update('tsprachwerte', 'cName', 'invalidHash', (object)['kSprachsektion' => 6]);
        $this->getDB()->update('tsprachwerte', 'cName', 'invalidCustomer', (object)['kSprachsektion' => 6]);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->getDB()->update('tsprachwerte', 'cName', 'invalidHash', (object)['kSprachsektion' => 4]);
        $this->getDB()->update('tsprachwerte', 'cName', 'invalidCustomer', (object)['kSprachsektion' => 4]);
    }
}
