<?php

/**
 * language setting invalidHash
 *
 * @author ms
 * @created Thu, 07 Apr 2016 12:57:33 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160406093712
 */
class Migration20160407125733 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'productDetails',
            'invalidHash',
            'Ung&uuml;ltiger Hash &uuml;bergeben - Eventuell ist Ihr Link abgelaufen. '
            . 'Versuchen Sie bitte erneut, Ihr Passwort zurückzusetzen.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
