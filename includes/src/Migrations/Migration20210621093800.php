<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210621093800
 */
class Migration20210621093800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add pw too long lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'login', 'passwordTooLong', 'Das Passwort ist zu lang.');
        $this->setLocalization('eng', 'login', 'passwordTooLong', 'Password too long.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('passwordTooLong', 'login');
    }
}
