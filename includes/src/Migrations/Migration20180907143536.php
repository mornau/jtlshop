<?php

/**
 * Setting for SEO handling of articles which are sold out
 *
 * @author fp
 * @created Mon, 19 Mar 2018 12:03:12 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180907143536
 */
class Migration20180907143536 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Setting for SEO handling of articles which are sold out';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'artikel_artikelanzeigefilter_seo',
            'seo',
            \CONF_GLOBAL,
            'Direktaufruf ausverkaufter Artikel',
            'selectbox',
            215,
            (object)[
                'cBeschreibung' => 'Methode beim Direktaufruf (über Artikel-URL) ausverkaufter Artikel. ' .
                    '(Ist nur wirksam, wenn "Artikelanzeigefilter" aktiv ist.)',
                'inputOptions'  => [
                    '404' => 'Seite nicht gefunden (404 Not Found)',
                    'seo' => 'Artikel-Detailseite bleibt erreichbar',
                ],
            ],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('artikel_artikelanzeigefilter_seo');
    }
}
