<?php

/**
 * Fix typo in subject of english version of mail template for delete customer account
 *
 * @author Stefan Langkau
 * @created Mon, 17 Oct 2022 08:22:22 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20221017082222
 */
class Migration20221017082222 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'Fix typo in subject of english version of '
            . 'mail template for delete customer account';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE temailvorlagespracheoriginal emailOrig
                    INNER JOIN
                tsprache ON tsprache.kSprache = emailOrig.kSprache
                    INNER JOIN
                temailvorlage ON temailvorlage.kEmailvorlage = emailOrig.kEmailvorlage 
            SET 
                emailOrig.cBetreff = 'Your account has been deleted'
            WHERE
                tsprache.cISO = 'eng'
                    AND temailvorlage.cModulId = 'core_jtl_account_geloescht'
                    AND emailOrig.cBetreff = 'You account has been deleted'"
        );

        $this->execute(
            "UPDATE temailvorlagesprache emailCust
                    INNER JOIN
                tsprache ON tsprache.kSprache = emailCust.kSprache
                    INNER JOIN
                temailvorlage ON temailvorlage.kEmailvorlage = emailCust.kEmailvorlage 
            SET 
                emailCust.cBetreff = 'Your account has been deleted'
            WHERE
                tsprache.cISO = 'eng'
                    AND temailvorlage.cModulId = 'core_jtl_account_geloescht'
                    AND emailCust.cBetreff = 'You account has been deleted'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE temailvorlagespracheoriginal emailOrig
                    INNER JOIN
                tsprache ON tsprache.kSprache = emailOrig.kSprache
                    INNER JOIN
                temailvorlage ON temailvorlage.kEmailvorlage = emailOrig.kEmailvorlage 
            SET 
                emailOrig.cBetreff = 'You account has been deleted'
            WHERE
                tsprache.cISO = 'eng'
                    AND temailvorlage.cModulId = 'core_jtl_account_geloescht'
                    AND emailOrig.cBetreff = 'Your account has been deleted'"
        );

        $this->execute(
            "UPDATE temailvorlagesprache emailCust
                    INNER JOIN
                tsprache ON tsprache.kSprache = emailCust.kSprache
                    INNER JOIN
                temailvorlage ON temailvorlage.kEmailvorlage = emailCust.kEmailvorlage 
            SET 
                emailCust.cBetreff = 'You account has been deleted'
            WHERE
                tsprache.cISO = 'eng'
                    AND temailvorlage.cModulId = 'core_jtl_account_geloescht'
                    AND emailCust.cBetreff = 'Your account has been deleted'"
        );
    }
}
