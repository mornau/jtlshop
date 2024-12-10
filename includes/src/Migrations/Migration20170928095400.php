<?php

/**
 * removes option "Zertifikat ausgestellt auf www/nicht-www"
 *
 * @author fm
 * @created Wed, 28 Sep 2017 09:28:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170928095400
 */
class Migration20170928095400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'global_ssl_www',
            '',
            \CONF_GLOBAL,
            'Zertifikat ausgestellt auf',
            'selectbox',
            541,
            (object)[
                'cBeschreibung' => 'Diese Einstellung ist nur gültig, wenn Sie ein eigenes Zertifikat nutzen. ' .
                    'Geben Sie hier an, ob es auf die Domain mit www. davor ausgestellt wurde.',
                'inputOptions'  => [
                    'www.' => 'Zertifikat ausgestellt auf Domain mit www',
                    ''     => 'Zertifikat ausgestellt auf Domain ohne www',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('global_ssl_www');
    }
}
