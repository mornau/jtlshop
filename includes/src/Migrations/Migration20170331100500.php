<?php

/**
 * Changes free gift descriptions
 *
 * @author ms
 * @created Fri, 31 Mar 2017 10:05:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170331100500
 */
class Migration20170331100500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Change free gift descriptions to clarify free gifts '
            . 'are based on the value of goods';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'global',
            'freeGiftFromOrderValue',
            'Im Warenkorb können Sie aus folgenden Gratisgeschenken wählen, sofern Ihr Warenkorb '
            . 'über den erforderlichen Warenwert kommt.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'freeGiftFromOrderValueBasket',
            'Wählen Sie ein Gratisgeschenk'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'freegiftsMinimum',
            'Der Gratisartikel-Mindestwarenwert ist nicht erreicht.'
        );

        $this->setLocalization(
            'eng',
            'errorMessages',
            'freegiftsMinimum',
            'Minimum value of goods not reached for this free gift.'
        );

        $this->execute(
            "UPDATE teinstellungenconf
                SET cBeschreibung = 'Soll die Funktion der Gratisgeschenke genutzt werden?'
                WHERE cWertName = 'sonstiges_gratisgeschenk_nutzen'"
        );
        $this->execute(
            "UPDATE teinstellungenconf 
                SET cName = 'Anzahl Gratisgeschenke in der Übersichtsseite',
                 cBeschreibung = 'Wieviele Gratisgeschenke sollen in der Übersichtsseite angezeigt werden? 0 = Alle'
                 WHERE cWertName = 'sonstiges_gratisgeschenk_anzahl'"
        );
        $this->execute(
            "UPDATE teinstellungenconf
                SET cName = 'Sortierung der Gratisgeschenke nach'
                WHERE cWertName = 'sonstiges_gratisgeschenk_sortierung'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization(
            'ger',
            'global',
            'freeGiftFromOrderValue',
            'Im Warenkorb können Sie aus folgenden Gratisgeschenken wählen, '
            . 'sofern Ihr Warenkorb über den erforderlichen Bestellwert kommt.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'freeGiftFromOrderValueBasket',
            'Wählen Sie ein Gratis Geschenk'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'freegiftsMinimum',
            'Der Gratisartikel-Mindestbestellwert ist nicht erreicht.'
        );

        $this->setLocalization(
            'eng',
            'errorMessages',
            'freegiftsMinimum',
            'Minimum shopping cart value not reached for this free gift.'
        );

        $this->execute(
            'UPDATE teinstellungenconf
                SET cBeschreibung = "Solle die Funktion der Gratisgeschenke genutzt werden?"
                WHERE cWertName="sonstiges_gratisgeschenk_nutzen";'
        );
        $this->execute(
            "UPDATE teinstellungenconf
                SET cName = 'Anzahl Gratis Geschenke in der Übersichtsseite',
                    cBeschreibung = 'Wieviele Gratis Geschenke sollen in der Übersichtsseite angezeigt werden? 0 = Alle'
                WHERE cWertName = 'sonstiges_gratisgeschenk_anzahl'"
        );
        $this->execute(
            "UPDATE teinstellungenconf
                SET cName = 'Sortierung der Gratis Geschenk Artikel nach' 
                WHERE cWertName = 'sonstiges_gratisgeschenk_sortierung'"
        );
    }
}
