<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230616141800
 */
class Migration20230616141800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add redis user config';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'caching_redis_user',
            '',
            \CONF_CACHING,
            'Username für Redis',
            'text',
            48,
            (object)['nStandardAnzeigen' => 0]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('caching_redis_user');
    }
}
