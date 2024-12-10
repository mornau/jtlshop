<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201014083300
 */
class Migration20201014083300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add confirm password lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'forgot password',
            'confirmNewPassword',
            'Neues Passwort übernehmen'
        );
        $this->setLocalization(
            'eng',
            'forgot password',
            'confirmNewPassword',
            'Confirm new password'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('confirmNewPassword', 'forgot password');
    }
}
