<?php

/**
 * Add language variables for birthday date
 *
 * @author dr
 * @created Mon, 16 Jan 2017 14:56:38 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170116145638
 */
class Migration20170116145638 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add language variables for birthday date';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'account data', 'birthdayFormat', 'TT.MM.JJJJ');
        $this->setLocalization('eng', 'account data', 'birthdayFormat', 'DD.MM.YYYY');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('birthdayFormat');
    }
}
