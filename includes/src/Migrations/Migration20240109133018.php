<?php

/**
 * Add column priority to temailvorlage
 *
 * @author sl
 * @created Tue, 09 Jan 2024 13:30:18 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240109133018
 */
class Migration20240109133018 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'Add column priority to temailvorlage and emails';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->db->executeQuery(
            'ALTER TABLE `temailvorlage`
                ADD COLUMN `nPrio` TINYINT NULL DEFAULT 100 AFTER `kPlugin`'
        );
        $this->db->executeQuery(
            'ALTER TABLE `emails`
                ADD COLUMN `priority` TINYINT NULL DEFAULT 100 AFTER `customerGroupID`'
        );
        $this->db->executeQuery(
            'UPDATE `temailvorlage`
                SET nPrio = 0 WHERE `cModulId` = \'core_jtl_passwort_vergessen\''
        );
        $this->db->executeQuery(
            'UPDATE `temailvorlage`
                SET nPrio = 1 WHERE `cModulId` = \'core_jtl_bestellbestaetigung\''
        );

        $this->setConfig(
            'email_send_immediately',
            'Y',
            \CONF_EMAILS,
            'Emails direkt versenden',
            'selectbox',
            121,
            (object)[
                'inputOptions' => [
                    'Y' => 'Ja, alle E-Mails sofort versenden',
                    'N' => 'Nein, nur priorisierte E-Mails sofort versenden',
                ]
            ],
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('email_send_immediately');

        $this->db->executeQuery('ALTER TABLE temailvorlage DROP COLUMN `nPrio`');
        $this->db->executeQuery('ALTER TABLE emails DROP COLUMN `priority`');
    }
}
