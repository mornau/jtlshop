<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210205115800
 */
class Migration20210205115800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add review all lang setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'bewertung_alle_sprachen',
            'Y',
            \CONF_BEWERTUNG,
            'Bewertungen aller Sprachen auf Artikeldetailseite anzeigen.',
            'selectbox',
            130,
            (object)[
                'cBeschreibung' => 'Bewertungen aller Sprachen auf Artikeldetailseite anzeigen.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );

        $this->setLocalization('ger', 'product rating', 'reviewsInAllLang', 'Alle Bewertungen:');
        $this->setLocalization('eng', 'product rating', 'reviewsInAllLang', 'All reviews:');
        $this->setLocalization('ger', 'product rating', 'noReviewsInAllLang', 'Es gibt noch keine Bewertungen.');
        $this->setLocalization('eng', 'product rating', 'noReviewsInAllLang', 'There are no reviews yet.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('bewertung_alle_sprachen');

        $this->removeLocalization('reviewsInAllLang', 'product rating');
        $this->removeLocalization('noReviewsInAllLang', 'product rating');
    }
}
