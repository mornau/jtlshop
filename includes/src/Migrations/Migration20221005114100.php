<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20221005114100
 */
class Migration20221005114100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add configurator lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'productDetails', 'configIsOptional', 'Diese Konfigurationsgruppe ist optional.');
        $this->setLocalization('eng', 'productDetails', 'configIsOptional', 'This configuration group is optional.');
        $this->setLocalization(
            'ger',
            'productDetails',
            'configIsNotCorrect',
            'Diese Konfigurationsgruppe ist noch nicht richtig eingestellt.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'configIsNotCorrect',
            'This configuration group must be configured correctly.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('configIsOptional', 'productDetails');
        $this->removeLocalization('configIsNotCorrect', 'productDetails');
    }
}
