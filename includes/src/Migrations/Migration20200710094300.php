<?php

/**
 * Remove unused settings
 *
 * @author mh
 * @created Fr, 12 Jun 2020 15:00:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200710094300
 */
class Migration20200710094300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove unused settings';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('news_kategorie_boxanzeigen');
        $this->removeConfig('news_sicherheitscode');
        $this->removeConfig('artikeldetails_anzahl_pfeile');
        $this->removeConfig('artikeluebersicht_anzahl_pfeile');
        $this->removeConfig('configgroup_1_saved_cart');
        $this->removeConfig('sitemap_weitereseiten_artikeluebersicht');

        $this->getDB()->update(
            'teinstellungenconf',
            'cWertName',
            'warenkorbpers_nutzen',
            (object)[
                'kEinstellungenSektion' => \CONF_KAUFABWICKLUNG,
                'nSort'                 => 275,
                'nModul'                => 0
            ]
        );
        $this->getDB()->update(
            'teinstellungen',
            'cName',
            'warenkorbpers_nutzen',
            (object)['kEinstellungenSektion' => \CONF_KAUFABWICKLUNG]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'news_kategorie_boxanzeigen',
            'Y',
            \CONF_NEWS,
            'Newskategorien in einer Box anzeigen',
            'selectbox',
            110,
            (object)[
                'cBeschreibung' => 'Möchten Sie eine Übersicht aller Newskategorien in einer Box angezeigt haben?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
        $this->setConfig(
            'news_sicherheitscode',
            'Y',
            \CONF_NEWS,
            'Spamschutz aktivieren',
            'selectbox',
            115,
            (object)[
                'cBeschreibung' => 'Soll beim Erstellen eines Kommentares ein Sicherheitscode abgefragt werden, '
                    . 'damit das Formular akzeptiert und abgesendet wird?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
        $this->setConfig(
            'artikeldetails_anzahl_pfeile',
            'Y',
            \CONF_ARTIKELDETAILS,
            'Pfeilbuttons für die Artikelanzahl',
            'selectbox',
            490,
            (object)[
                'cBeschreibung' => 'Wie sollen die Pfeile für die Anzahl in den Artikeldetails angezeigt werden.',
                'inputOptions'  => [
                    'Y' => 'Immer',
                    'I' => 'Wenn Abnahmeintervall vorhanden',
                    'N' => 'Nicht anzeigen',
                ],
            ]
        );
        $this->setConfig(
            'artikeluebersicht_anzahl_pfeile',
            'Y',
            \CONF_ARTIKELUEBERSICHT,
            'Pfeilbuttons für die Artikelanzahl',
            'selectbox',
            460,
            (object)[
                'cBeschreibung' => 'Wie sollen die Pfeile für die Anzahl in der Artikelübersicht angezeigt werden.',
                'inputOptions'  => [
                    'Y' => 'Immer',
                    'I' => 'Wenn Abnahmeintervall vorhanden',
                    'N' => 'Nicht anzeigen',
                ],
            ]
        );
        $this->setConfig(
            'sitemap_weitereseiten_artikeluebersicht',
            'Y',
            \CONF_SITEMAP,
            'Seiten der Artikelübersichten in Sitemap aufnehmen',
            'selectbox',
            90,
            (object)[
                'cBeschreibung' => 'Sollen Seiten aus der Artikelübersicht wie z.b. Hersteller, Kategorien '
                    . 'mit in die Sitemap aufgenommen werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
        $this->setConfig(
            'configgroup_1_saved_cart',
            'Gespeicherter Warenkorb',
            \CONF_GLOBAL,
            'Gespeicherter Warenkorb',
            null,
            800,
            (object)['cConf' => 'N']
        );

        $this->getDB()->update(
            'teinstellungenconf',
            'cWertName',
            'warenkorbpers_nutzen',
            (object)[
                'kEinstellungenSektion' => \CONF_GLOBAL,
                'nSort'                 => 810,
                'nModul'                => 1
            ]
        );
        $this->getDB()->update(
            'teinstellungen',
            'cName',
            'warenkorbpers_nutzen',
            (object)['kEinstellungenSektion' => \CONF_GLOBAL]
        );
    }
}
