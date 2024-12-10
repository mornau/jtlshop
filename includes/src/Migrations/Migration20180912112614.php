<?php

/**
 * change_check_city_description
 *
 * @author mh
 * @created Wed, 12 Sep 2018 11:26:14 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180912112614
 */
class Migration20180912112614 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Change check city description';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf
                SET cBeschreibung = 'Fehlermeldung ausgeben, wenn eingegebene Stadt eine Zahl enthält.'
                WHERE cWertName = 'kundenregistrierung_pruefen_ort'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf
                SET cBeschreibung = 'Wenn die eingegebene Stadt eine Zahle enthät abbrechen'
                WHERE cWertName = 'kundenregistrierung_pruefen_ort'"
        );
    }
}
