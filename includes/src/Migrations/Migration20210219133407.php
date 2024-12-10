<?php

/**
 * change_vorausgewaehltes_land_conf_to_selectbox
 *
 * @author je
 * @created Fri, 19 Feb 2021 13:34:07 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210219133407
 */
class Migration20210219133407 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'je';
    }

    public function getDescription(): string
    {
        return 'Change default country config to selectbox';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf
                SET cInputTyp = 'selectbox'
                WHERE cWertName = 'kundenregistrierung_standardland'"
        );
        $this->execute(
            "UPDATE teinstellungenconf
                SET cInputTyp = 'selectbox'
                WHERE cWertName = 'lieferadresse_abfragen_standardland'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE teinstellungenconf
                SET cInputTyp = 'text'
                WHERE cWertName = 'kundenregistrierung_standardland'"
        );
        $this->execute(
            "UPDATE teinstellungenconf
                SET cInputTyp = 'text'
                WHERE cWertName = 'lieferadresse_abfragen_standardland'"
        );
    }
}
