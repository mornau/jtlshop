<?php

/**
 * add mandatory consent item
 *
 * @author dr
 * @created Thu, 01 Jun 2023 10:12:07 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230601101207
 */
class Migration20230601101207 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'add mandatory consent item';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $id = $this->getDB()->getLastInsertedID("INSERT INTO tconsent (itemID, active) VALUES ('necessary', 0)");
        $this->execute(
            'INSERT INTO tconsentlocalization 
                (consentID, languageID, privacyPolicy, description, purpose, name)
             VALUES (' . $id . ", 1, '',
                 'Technisch notwendige Cookies ermöglichen grundlegende Funktionen und sind für den einwandfreien   
                  Betrieb der Website erforderlich.',
                 '',
                 'Technisch notwendig')"
        );
        $this->execute(
            'INSERT INTO tconsentlocalization
                (consentID, languageID, privacyPolicy, description, purpose, name)
             VALUES (' . $id . ", 2, '',
                 'Strictly necessary cookies are those that enable the basic functions of a website. Without them, the
                  website will not work properly.',
                 '',
                 'Strictly necessary cookies')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM tconsent WHERE itemID = 'necessary'");
    }
}
