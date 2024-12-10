<?php

/**
 * add_lang_invalid_url
 *
 * @author mh
 * @created Tue, 28 Aug 2018 13:05:42 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180828130542
 */
class Migration20180828130542 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang variable invalidURL';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'invalidURL', 'Bitte geben Sie eine valide URL ein.');
        $this->setLocalization('eng', 'global', 'invalidURL', 'Please enter a valid url.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('invalidURL');
    }
}
