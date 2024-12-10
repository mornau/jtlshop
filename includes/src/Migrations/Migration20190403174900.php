<?php

/**
 * @author fm
 * @created Wed, 03 Apr 2019 17:49:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190403174900
 */
class Migration20190403174900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove old exports';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "DELETE FROM texportformat 
                WHERE dZuletztErstellt IS NULL 
                AND kPlugin = 0
                AND cName IN ('Hardwareschotte', 'Kelkoo', 'Become Europe (become.eu)',
                              'Billiger', 'Geizhals', 'Preisauskunft',
                              'Preistrend', 'Shopboy', 'Idealo', 'Preisroboter', 'Milando', 'Channelpilot',
                             'Preissuchmaschine', 'Elm@r Produktdatei', 'Yatego Neu', 'LeGuide.com', 'Twenga'
                             )"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
