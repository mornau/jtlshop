<?php

/**
 * Reworked frontend texts
 *
 * @author mh
 * @created Wed, 20 Nov 2019 11:08:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191120110800
 */
class Migration20191120110800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Reworked frontend texts';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'global',
            'aaSelectBTN',
            'Auswählen'
        );
        $this->setLocalization(
            'ger',
            'global',
            'accountCreated',
            'Ihr Kundenkonto wurde erstellt.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'accountInactive',
            'Ihr Kundenkonto wurde deaktiviert.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'accountLocked',
            'Ihr Kundenkonto wurde gesperrt.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'adminMaintenanceMode',
            'Der Wartungsmodus des Onlineshops ist aktiv. Da Sie als Administrator angemeldet sind, '
            . 'können Sie trotzdem alle Funktionen des Onlineshops bedienen.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ajaxcheckoutChangemethode',
            'Ändern'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ajaxLoading',
            'Wird geladen…'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ampelGelb',
            'Knapper Lagerbestand'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ampelGruen',
            'Sofort verfügbar'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ampelRot',
            'Momentan nicht verfügbar'
        );
        $this->setLocalization(
            'ger',
            'global',
            'applyChanges',
            'Änderungen übernehmen'
        );
        $this->setLocalization(
            'ger',
            'global',
            'asc',
            'aufsteigend'
        );
        $this->setLocalization(
            'ger',
            'global',
            'basketCustomerWhoBoughtXBoughtAlsoY',
            'Kunden kauften dazu folgende Artikel'
        );
        $this->setLocalization(
            'ger',
            'global',
            'blockedEmail',
            'E-Mail-Adresse ist gesperrt.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'categoryoverviewSub',
            'Wählen Sie eine Kategorie!'
        );
        $this->setLocalization(
            'ger',
            'global',
            'clickImgToZoom',
            'Klicken Sie auf das Bild, um es in der Galerieansicht zu öffnen.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'compareListNoItems',
            'Es befinden sich noch keine Artikel auf Ihrer Vergleichsliste.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'configure',
            'Konfigurieren'
        );
        $this->setLocalization(
            'ger',
            'global',
            'copyright',
            '© 2019'
        );
        $this->setLocalization(
            'ger',
            'global',
            'copyrightName',
            'JTL-Shop'
        );
        $this->setLocalization(
            'ger',
            'global',
            'counter',
            'Besucherzähler'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr1',
            'Coupon ist nicht aktiv.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr10',
            'Der Coupon gilt nicht für die angegebene Lieferadresse.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr12',
            'Der Coupon gilt nicht für den aktuellen Warenkorb (gilt nur für bestimmte Hersteller).'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr2',
            'Der Coupon ist nicht mehr gültig (Datum abgelaufen).'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr3',
            'Der Coupon ist nicht mehr gültig.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr4',
            'Der für diesen Coupon benötigte Mindestbestellwert wurde noch nicht erreicht.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr5',
            'Der Coupon gilt nicht für die aktuelle Kundengruppe.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr6',
            'Die maximale Anzahl der Verwendungen für den Coupon wurde erreicht.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr7',
            'Der Coupon gilt nicht für den aktuellen Warenkorb (gilt nur für bestimmte Artikel).'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr8',
            'Der Coupon gilt nicht für den aktuellen Warenkorb (gilt nur für bestimmte Kategorien).'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr9',
            'Der Coupon gilt nicht für Ihr Kundenkonto.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponErr99',
            'Unbekannter Coupon-Fehler. Bitte wiederholen Sie die Eingabe oder wenden Sie sich ggf. an den Support.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'couponSucc1',
            'Ihr Versandkostenfrei-Coupon wurde für folgende Lieferländer freigeschaltet:'
        );
        $this->setLocalization(
            'ger',
            'global',
            'csrfValidationFailed',
            'Die Anfrage konnte nicht verarbeitet werden. Der Grund hierfür kann sein, dass die Anfrage '
            . 'von einer unbekannten Quelle gestellt wurde oder die Website-Sitzung zwischenzeitlich abgelaufen ist. '
            . 'Bitte versuchen Sie es erneut.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'dateOfIssue',
            'Voraussichtliches Erscheinungsdatum:'
        );
        $this->setLocalization(
            'ger',
            'global',
            'delete',
            'Entfernen'
        );
        $this->setLocalization(
            'ger',
            'global',
            'details',
            'Zum Artikel'
        );
        $this->setLocalization(
            'ger',
            'global',
            'dlErrorCustomerNotMatch',
            'Der angemeldete Kunde und die Berechtigung der Download-Datei stimmen nicht überein.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'dlErrorDownloadLimitReached',
            'Die maximale Download-Anzahl wurde erreicht.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'dlErrorDownloadNotFound',
            'Der Artikel mit diesem Download existiert nicht.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'dlErrorOrderNotFound',
            'Ihre Bestellung konnte nicht gefunden werden.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'dlErrorValidityReached',
            'Die Gültigkeitsdauer wurde überschritten.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'dlErrorWrongParameter',
            'Die Parameter sind ungültig. Es wurde keine Datei zu diesem Download-Link gefunden.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'downloadLimit',
            'Download-Zahl'
        );
        $this->setLocalization(
            'ger',
            'global',
            'downloadPending',
            'Zahlungseingang ausstehend'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ean',
            'GTIN'
        );
        $this->setLocalization(
            'ger',
            'global',
            'eanNotExist',
            'Leider existiert in unserem Sortiment kein Artikel mit folgender Artikelnummer/GTIN:'
        );
        $this->setLocalization(
            'ger',
            'global',
            'edit',
            'Bearbeiten'
        );
        $this->setLocalization(
            'ger',
            'global',
            'else',
            'sonst'
        );
        $this->setLocalization(
            'ger',
            'global',
            'emailadress',
            'E-Mail-Adresse'
        );
        $this->setLocalization(
            'ger',
            'global',
            'estimateShippingCostsNote',
            'Die Versandkosten können erst ermittelt werden, wenn sich Artikel im Warenkorb befinden.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'expressionHasTo',
            'Der Suchbegriff muss mindestens aus'
        );
        $this->setLocalization(
            'ger',
            'global',
            'filterAndSort',
            'Filter und Sortierung'
        );
        $this->setLocalization(
            'ger',
            'global',
            'findProduct',
            'Artikel suchen'
        );
        $this->setLocalization(
            'ger',
            'global',
            'firstReview',
            'Geben Sie die erste Bewertung für diesen Artikel ab und helfen Sie anderen bei der Kaufentscheidung!'
        );
        $this->setLocalization(
            'ger',
            'global',
            'freeGiftFrom1',
            'Ab Bestellwert von'
        );
        $this->setLocalization(
            'ger',
            'global',
            'freeGiftFromOrderValue',
            'Im Warenkorb können Sie aus folgenden Gratisgeschenken wählen,'
            . ' sofern Ihr Warenkorb den erforderlichen Warenwert erreicht hat.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'freeGiftFromOrderValueBasket',
            'Wählen Sie ein Gratisgeschenk:'
        );
        $this->setLocalization(
            'ger',
            'global',
            'incorrectEmail',
            'Es existiert kein Kunde mit der angegebenen E-Mail-Adresse. Bitte versuchen Sie es erneut.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'incorrectEmailPlz',
            'Es existiert kein Kunde mit der angegebenen E-Mail-Adresse und PLZ. Bitte versuchen Sie es erneut.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'incorrectLogin',
            'Benutzername und Passwort stimmen nicht überein. Bitte versuchen Sie es erneut.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'inStock',
            'Auf Lager'
        );
        $this->setLocalization(
            'ger',
            'global',
            'invalidDate',
            'Ungültiges Datum'
        );
        $this->setLocalization(
            'ger',
            'global',
            'invalidDateformat',
            'Geben Sie das Datum im Format TT.MM.JJJJ ein, z.B. 04.11.1981.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'invalidEmail',
            'Bitte geben Sie eine gültige E-Mail-Adresse an.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'invalidInteger',
            'Bitte geben Sie eine Zahl ein.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'invalidTel',
            'Bitte geben Sie Ihre Nummer nur in Ziffern ein.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'invalidToken',
            'Der eingegebene Sicherheitscode ist ungültig.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'invalidURL',
            'Bitte geben Sie eine gültige URL ein.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'language',
            'Sprache'
        );
        $this->setLocalization(
            'ger',
            'global',
            'loginNotActivated',
            'Ihr Kundenkonto wurde noch nicht freigeschaltet.'
            . ' Bitte versuchen Sie es zu einem späteren Zeitpunkt erneut.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'maintenanceModeActive',
            'Dieser Onlineshop befindet sich im Wartungsmodus.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'manufacturer',
            'Herstellerübersicht'
        );
        $this->setLocalization(
            'ger',
            'global',
            'myWishlists',
            'Meine Wunschzettel'
        );
        $this->setLocalization(
            'ger',
            'global',
            'newProducts',
            'Neu im Sortiment'
        );
        $this->setLocalization(
            'ger',
            'global',
            'newsArchiv',
            'News-Archiv'
        );
        $this->setLocalization(
            'ger',
            'global',
            'newsArchivDesc',
            'Alle News-Beiträge im Archiv '
        );
        $this->setLocalization(
            'ger',
            'global',
            'newsBoxCatOverview',
            'News: Kategorien des News-Systems'
        );
        $this->setLocalization(
            'ger',
            'global',
            'newsBoxMonthOverview',
            'News: Monate mit Beiträgen'
        );
        $this->setLocalization(
            'ger',
            'global',
            'newsletterhistory',
            'Newsletter-Archiv'
        );
        $this->setLocalization(
            'ger',
            'global',
            'newsletterhistoryback',
            'Zurück'
        );
        $this->setLocalization(
            'ger',
            'global',
            'noEntriesAvailable',
            'Keine Einträge vorhanden.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'noFilterResults',
            'Für diesen Filter wurden keine Ergebnisse gefunden.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'noShippingcostsTo',
            'Versandkostenfreie Lieferung'
        );
        $this->setLocalization(
            'ger',
            'global',
            'notAvailableInSelection',
            'Ihre Auswahl ist leider nicht verfügbar.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'oldPrice',
            'Alter Preis'
        );
        $this->setLocalization(
            'ger',
            'global',
            'paginationEntriesPerPage',
            'Einträge pro Seite'
        );
        $this->setLocalization(
            'ger',
            'global',
            'paginationTotalEntries',
            'Einträge insgesamt:'
        );
        $this->setLocalization(
            'ger',
            'global',
            'parseTextNoLinkID',
            'Ungültiger Link'
        );
        $this->setLocalization(
            'ger',
            'global',
            'payWithPaypal',
            'Jetzt mit PayPal bezahlen'
        );
        $this->setLocalization(
            'ger',
            'global',
            'priceHidden',
            'Preise nach Anmeldung sichtbar'
        );
        $this->setLocalization(
            'ger',
            'global',
            'productAddedToCart',
            'wurde erfolgreich in den Warenkorb gelegt!'
        );
        $this->setLocalization(
            'ger',
            'global',
            'productAvailable',
            'Verfügbar'
        );
        $this->setLocalization(
            'ger',
            'global',
            'productNo',
            'Artikelnummer'
        );
        $this->setLocalization(
            'ger',
            'global',
            'productNoEAN',
            'Artikelnummer / GTIN'
        );
        $this->setLocalization(
            'ger',
            'global',
            'productNoExtraShippingNotice',
            'Es fallen keine zusätzlichen Versandkosten an.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'productNotAvailable',
            'Artikel vergriffen'
        );
        $this->setLocalization(
            'ger',
            'global',
            'productsFrom',
            'Artikel von'
        );
        $this->setLocalization(
            'ger',
            'global',
            'productsTaggedAs',
            'Artikel mit Tag'
        );
        $this->setLocalization(
            'ger',
            'global',
            'productsWith',
            'Artikel mit'
        );
        $this->setLocalization(
            'ger',
            'global',
            'questiontoproductSuccessful',
            'Ihre Frage wurde erfolgreich übermittelt. Sie werden in Kürze eine E-Mail von uns erhalten.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'registerNow',
            'Jetzt registrieren!'
        );
        $this->setLocalization(
            'ger',
            'global',
            'resetSelection',
            'Auswahl zurücksetzen'
        );
        $this->setLocalization(
            'ger',
            'global',
            'selectCurrency',
            'Bitte wählen Sie eine Währung.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'selectLang',
            'Bitte wählen Sie eine Sprache.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'selectManufacturer',
            'Bitte wählen Sie einen Hersteller.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'selectUpdateFile',
            'Bitte wählen Sie eine Datei aus.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'shippingTime',
            'Errechnete Lieferzeit'
        );
        $this->setLocalization(
            'ger',
            'global',
            'shippingTimeLP',
            'Lieferzeit'
        );
        $this->setLocalization(
            'ger',
            'global',
            'showAllNewProducts',
            'Zeige alle neuen Artikel'
        );
        $this->setLocalization(
            'ger',
            'global',
            'showAllProductsTaggedWith',
            'Zeige alle getaggten Artikel mit'
        );
        $this->setLocalization(
            'ger',
            'global',
            'showAllTopOffers',
            'Zeige alle Top-Angebote'
        );
        $this->setLocalization(
            'ger',
            'global',
            'showAllUpcomingProducts',
            'Zeige alle in Kürze verfügbaren Artikel'
        );
        $this->setLocalization(
            'ger',
            'global',
            'showProducts',
            'Zeige Artikel'
        );
        $this->setLocalization(
            'ger',
            'global',
            'sitemapNewsCats',
            'News-Kategorien'
        );
        $this->setLocalization(
            'ger',
            'global',
            'sortAvailability',
            'Lagerbestand'
        );
        $this->setLocalization(
            'ger',
            'global',
            'sortEan',
            'GTIN'
        );
        $this->setLocalization(
            'ger',
            'global',
            'sortNameAsc',
            'Artikelname von A bis Z'
        );
        $this->setLocalization(
            'ger',
            'global',
            'sortNameDesc',
            'Artikelname von Z bis A'
        );
        $this->setLocalization(
            'ger',
            'global',
            'sortNewestFirst',
            'Neueste zuerst'
        );
        $this->setLocalization(
            'ger',
            'global',
            'sortPriceAsc',
            'Preis aufsteigend'
        );
        $this->setLocalization(
            'ger',
            'global',
            'sortPriceDesc',
            'Preis absteigend'
        );
        $this->setLocalization(
            'ger',
            'global',
            'specificProducts',
            'Besondere Artikel'
        );
        $this->setLocalization(
            'ger',
            'global',
            'subtotal',
            'Summe'
        );
        $this->setLocalization(
            'ger',
            'global',
            'Suedamerika',
            'Südamerika'
        );
        $this->setLocalization(
            'ger',
            'global',
            'supplierStockNotice',
            'Muss bestellt werden. Ab Bestellung lieferbar in %s Tagen.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'switchToMobileTemplate',
            'Möchten Sie diese Website in einer für mobile Endgeräte optimierten Version aufrufen?'
        );
        $this->setLocalization(
            'ger',
            'global',
            'tagcloud',
            'Tag-Wolke'
        );
        $this->setLocalization(
            'ger',
            'global',
            'tagFilter',
            'Tag-Filter'
        );
        $this->setLocalization(
            'ger',
            'global',
            'topOffer',
            'Top-Angebot'
        );
        $this->setLocalization(
            'ger',
            'global',
            'topOffers',
            'Top-Angebote'
        );
        $this->setLocalization(
            'ger',
            'global',
            'toprating',
            'Top bewerteten Artikel'
        );
        $this->setLocalization(
            'ger',
            'global',
            'topsearch',
            'Top-Suchanfragen '
        );
        $this->setLocalization(
            'ger',
            'global',
            'toptags',
            'Am häufigsten vergebene Tags '
        );
        $this->setLocalization(
            'ger',
            'global',
            'ts_classic_text',
            'trägt das Trusted Shops Zertifikat mit Käuferschutz. Mehr…'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ts_comment',
            'Als zusätzlichen Service bieten wir Ihnen den Trusted Shops Käuferschutz an. '
            . 'Wir übernehmen alle Kosten dieser Garantie. Sie müssen sich lediglich anmelden.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ts_seal_classic_title',
            'Klicken Sie auf das Gütesiegel, um die Gültigkeit zu prüfen!'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ts_signtitle',
            'Trusted Shops Gütesiegel – Bitte hier Gültigkeit prüfen!'
        );
        $this->setLocalization(
            'ger',
            'global',
            'unlimited',
            'uneingeschränkt'
        );
        $this->setLocalization(
            'ger',
            'global',
            'uploadCanceled',
            'Der Datei-Upload wurde abgebrochen.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'uploadEmptyFile',
            'Die ausgewählte Datei ist leer.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'uploadError',
            'Der Upload konnte nicht durchgeführt werden. Bitte versuchen Sie es erneut.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ustIDCaseFive',
            'Die USt-IdNr. ist laut MIAS-Prüfung ungültig.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ustIDCaseTwo',
            'Die USt-IdNr. weist ein ungültiges Format auf.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ustIDCaseTwoB',
            'Für Ihr Land sollte die USt-IdNr. das folgende Format aufweisen:'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ustIDError100',
            'Die Länderkennung der USt-IdNr. muss mit zwei Großbuchstaben beginnen.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ustIDError110',
            'Die USt-IdNr. hat eine ungültige Länge.'
        );
        $this->setLocalization(
            'ger',
            'global',
            'ustIDError120',
            'Die USt-IdNr. entspricht nicht den Vorschriften Ihres Landes!<br>Der Fehler trat hier auf: '
        );
        $this->setLocalization(
            'ger',
            'global',
            'ustIDError130',
            'Es existiert kein Land mit folgender Kennung: '
        );
        $this->setLocalization(
            'ger',
            'global',
            'ustIDError200',
            'Der MIAS-Dienst Ihres Landes ist nicht erreichbar bis: '
        );
        $this->setLocalization(
            'ger',
            'global',
            'voucher',
            'Coupon'
        );
        $this->setLocalization(
            'ger',
            'global',
            'weightUnit',
            'kg'
        );
        $this->setLocalization(
            'ger',
            'global',
            'wrbform',
            'Muster-Widerrufsformular'
        );
        $this->setLocalization(
            'ger',
            'global',
            'yourSearch',
            'Ihr Suchbegriff lautete:'
        );
        $this->setLocalization(
            'ger',
            'productOverview',
            'addToCompare',
            'Auf die Vergleichsliste'
        );
        $this->setLocalization(
            'ger',
            'productOverview',
            'addToWishlist',
            'Auf den Wunschzettel'
        );
        $this->setLocalization(
            'ger',
            'productOverview',
            'noResults',
            'Leider wurde zu Ihrem Suchbegriff nichts gefunden. Bitte geben Sie einen anderen Suchbegriff ein.'
        );
        $this->setLocalization(
            'ger',
            'productOverview',
            'previous',
            'Zurück'
        );
        $this->setLocalization(
            'ger',
            'productOverview',
            'ribbon-5',
            'Bald verfügbar'
        );
        $this->setLocalization(
            'ger',
            'productOverview',
            'ribbon-9',
            'Vorbestellen'
        );
        $this->setLocalization(
            'ger',
            'productOverview',
            'searchAgain',
            'Erneut suchen'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'addToCompare',
            'Auf die Vergleichsliste'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'addToWishlist',
            'Auf den Wunschzettel'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'addYourTag',
            'Ihren Artikel-Tag hinzufügen'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'basketMatrix',
            'Bitte tragen Sie die gewünschten Mengen ein.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'buyProductBundle',
            'Kaufen Sie diesen Artikel im Set!'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'configChooseMaxComponents',
            'Bitte wählen Sie maximal %d.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'configChooseMinComponents',
            'Bitte wählen Sie mindestens %d.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'configChooseNComponents',
            'Bitte wählen Sie %d.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'configChooseOneComponent',
            'Bitte wählen Sie genau eine Komponente.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'configError',
            'Es ist ein Fehler bei der Konfiguration aufgetreten. Bitte versuchen Sie es erneut '
            . 'oder wenden Sie sich ggf. an den Support.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'customerWhoBoughtXBoughtAlsoY',
            'Kunden kauften dazu folgende Artikel:'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'dimensions2d',
            'Abmessungen (L×H)'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'inStock',
            'Auf Lager'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'integralQuantities',
            'Bei diesem Artikel ist die Stückzahl teilbar (z.B. 0,5).'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'maximalPurchase',
            'Sie können maximal %d %s von diesem Artikel erwerben.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'minimumPurchase',
            'Bitte beachten Sie die Mindestabnahme von %d %s.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'pleaseChooseVariation',
            'Bitte wählen Sie eine Variation.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'priceForAll',
            'Gesamtpreis'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'productOutOfStock',
            'Artikel zurzeit vergriffen'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'productQuestion',
            'Frage zum Artikel'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'productTags',
            'Artikel-Tags'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'productTagsDesc',
            'Andere Kunden markierten diesen Artikel mit diesen Tags. Artikel-Tags sind Stichwörter, '
            . 'unter denen andere Kunden die Artikel leichter finden können.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'productUnsaleable',
            'Dieser Artikel ist derzeit nicht verfügbar. Ob und wann dieser Artikel wieder erhältlich ist, '
            . 'steht nicht fest.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'productVote',
            'Bewertungen für diesen Artikel'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'recommendLogin',
            'Bitte melden Sie sich an.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'RelatedProducts',
            'Ähnliche Artikel'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'selectionNotAvailable',
            'Die gewählte Kombination ist nicht verfügbar.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'taglogin',
            'Anmelden'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'tagloginnow',
            'Bitte melden Sie sich an, um dem Artikel einen Tag hinzuzufügen. '
            . 'Artikel-Tags sind Stichwörter, unter denen andere Kunden die Artikel leichter finden können.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'takeHeedOfInterval',
            'Bitte beachten Sie das Abnahmeintervall von %d %s.'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'thisProductIsFrom',
            'Dieser Artikel ist erhältlich bei'
        );
        $this->setLocalization(
            'ger',
            'productDetails',
            'updatingStockInformation',
            'Lagerinformationen für Variationen werden geladen…'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'additionalCharges',
            'Sofern die Lieferung in das Nicht-EU-Ausland erfolgt, können weitere Zölle, '
            . 'Steuern oder Gebühren vom Kunden zu zahlen sein, jedoch nicht an den Anbieter, '
            . 'sondern an die dort zuständigen Zoll- bzw. Steuerbehörden. '
            . 'Wir empfehlen Ihnen, die Einzelheiten vor der Bestellung bei den Zoll- bzw. Steuerbehörden zu erfragen.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'banktransferDesc',
            'Wir werden Ihre Bestellung umgehend bearbeiten.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'cancellationPolicyNotice',
            'Ich habe die <a href="%s" %s>Widerrufsbelehrung</a> zur Kenntnis genommen.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'cashOnDeliveryDesc',
            'Wir werden Ihre Bestellung umgehend versenden.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'checkPLZCity',
            'Bitte überprüfen Sie PLZ und Ort.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'creditcardDesc',
            'Wir werden Ihre Bestellung umgehend bearbeiten.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'currentCoupon',
            'Bereits eingelöster Coupon: '
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'discountForArticle',
            'Gültig für: '
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'doFollowingBanktransfer',
            'Bitte führen Sie folgende Überweisung durch:'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'emptybasket',
            'Es befinden sich keine Artikel im Warenkorb.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'estimateShipping',
            'Versandkosten ermitteln'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'goOnShopping',
            'Weitere Artikel ansehen'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'goToStartpage',
            'Zur Startseite'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'guestOrRegistered',
            'Sie können als Gast bestellen oder ein neues Kundenkonto erstellen.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'iban',
            'IBAN'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'invalidCouponCode',
            'Der eingegebene Coupon-Code ist ungültig.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'invalidUserdata',
            'Der eingegebene Benutzername und/oder das Passwort ist falsch!'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'invoiceDesc',
            'Wir werden Ihre Bestellung umgehend bearbeiten.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'loginForRegisteredCustomersDesc',
            'Sie haben bereits bei uns bestellt und verfügen über ein Kundenkonto? Dann geben Sie hier Ihre Daten ein.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'minordernotreached',
            'Der Mindestbestellwert wurde nicht erreicht. Der Mindestbestellwert beträgt'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'missingFilesUpload',
            'Bitte laden Sie alle notwendigen Dateien hoch.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'missingProducts',
            'Einige Artikel in Ihrem Warenkorb sind bereits vergriffen. Ihr Warenkorb wurde angepasst.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'newEstimation',
            'Neue Versandkostenermittlung'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'noShippingAvailable',
            'Für diesen Zielort stehen keine Versandarten zur Verfügung. '
            . 'Bitte kontaktieren Sie uns direkt, um diese Bestellung abzuwickeln.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'orderCompletedPost',
            'Ihre Bestellung ist bei uns eingegangen.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'orderNotPossibleNow',
            'Sie haben vor Kurzem bereits eine Bestellung abgeschickt. '
            . 'Weitere Bestellungen sind erst nach 2 Minuten möglich. Wenn Sie vorher versuchen, '
            . 'erneut zu bestellen, wird die Wartezeit verdoppelt.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'orderStep0Title2',
            'Wählen Sie die Art, wie Sie bestellen möchten.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'paypalDesc',
            'Mit einem Klick auf diese Schaltfläche werden Sie zu PayPal weitergeleitet und '
            . 'können Ihre Bestellung sofort bezahlen.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'priceHasChanged',
            'Der Preis des Artikels „%s“ in Ihrem Warenkorb hat sich zwischenzeitlich geändert. '
            . 'Bitte prüfen Sie die Warenkorbpositionen.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'product',
            'Artikel'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'productShippingDesc',
            'Artikelabhängige Versandkosten'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'secureCheckout',
            'Sichere Bezahlung'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'shippingTo',
            'Lieferland'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'termsAndConditionsNotice',
            'Ich habe die <a href="%s" %s>AGB</a> gelesen und erkläre mit dem Absenden'
            . ' der Bestellung mein Einverständnis.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'useCoupon',
            'Coupon einlösen'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'wrongBIC',
            'Das Format der eingegebenen BIC ist ungültig.'
        );
        $this->setLocalization(
            'ger',
            'checkout',
            'wrongIban',
            'Das Format der eingegebenen IBAN ist ungültig.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'birthday',
            'Geburtsdatum'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'birthdayFormat',
            'TT.MM.JJJJ'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'continueOrder',
            'Weiter'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'coupon',
            'Coupon einlösen'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'couponCode',
            'Coupon-Code'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'couponDesc',
            'Falls Sie einen Coupon haben und einlösen möchten, geben Sie den Coupon-Code bitte hier ein.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'creditDesc',
            'Sie haben noch Guthaben auf Ihrem Kundenkonto. Falls Sie es mit dieser Bestellung verrechnen möchten, '
            . 'klicken Sie bitte auf nachfolgende Schaltfläche. '
        );
        $this->setLocalization(
            'ger',
            'account data',
            'customerOpenOrders',
            'Sie haben noch %d offene Bestellungen%s. Wenn Sie Ihr Kundenkonto jetzt löschen, werden Ihre '
            . 'restlichen Daten automatisch gelöscht, sobald alle Bestellungen abgeschlossen sind.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'customerOrdersInCancellationTime',
            ' und %d Bestellungen, deren Retourenfrist noch nicht abgelaufen ist'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'emailAlreadyExists',
            'Zu der von Ihnen eingegeben E-Mail-Adresse existiert bereits ein Kundenkonto in unserem Onlineshop. '
            . 'Wenn Sie die Bestellung mit Ihrem vorhandenem Kundenkonto abschließen möchten, melden Sie '
            . 'sich bitte mit Ihrer E-Mail-Adresse und Ihrem Passwort an. Wenn Sie als Gast fortfahren '
            . 'möchten, deaktivieren Sie die Option „Neues Kundenkonto erstellen“.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'emailNotAvailable',
            'Diese E-Mail-Adresse ist bereits vergeben.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'formToFast',
            'Entschuldigung, Sie sind etwas zu schnell!'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'invalidCustomer',
            'Ungültiger Kunde. Der Kunde ist gesperrt.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'invalidHash',
            'Ungültiger Hash übergeben. Eventuell ist Ihr Link abgelaufen. '
            . 'Versuchen Sie bitte erneut, Ihr Passwort zurückzusetzen.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'newsletterInfo',
            ', wenn Sie über aktuelle Angebote in unserem Onlineshop informiert werden möchten.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'noOrdersYet',
            'Sie habe noch keine Bestellung aufgegeben.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'noWishlist',
            'Es ist kein Wunschzettel vorhanden.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'read',
            'Lesen'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'receiverEmail',
            'Empfänger-E-Mail-Adresse'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'receiverName',
            'Name des Empfänger'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'senderEmail',
            'Absender-E-Mail-Adresse'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'senderName',
            'Name des Absenders'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'shippingAdressDesc',
            'Falls die Lieferadresse von der Rechnungsadresse abweicht, geben Sie die Rechnungsadresse bitte hier an.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'useCredit',
            'Verrechnetes Guthaben'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'wishlists',
            'Wunschzettel'
        );
        $this->setLocalization(
            'ger',
            'shipping payment',
            'accountNo',
            'Kontonummer'
        );
        $this->setLocalization(
            'ger',
            'shipping payment',
            'completeOrder',
            'Bestellung abschließen'
        );
        $this->setLocalization(
            'ger',
            'shipping payment',
            'confirmDataDesc',
            'Falls alle Angaben korrekt sind, können Sie die Bestellung abschließen.<br>'
            . 'Sie erhalten eine Bestätigungs-E-Mail von uns.'
        );
        $this->setLocalization(
            'ger',
            'login',
            'basket2PersMerge',
            'Möchten Sie den gespeicherten Warenkorb mit Ihrem aktuellen Warenkorb zusammenfassen?'
        );
        $this->setLocalization(
            'ger',
            'login',
            'changepasswordPassTooShort',
            'Ihr neues Passwort ist zu kurz. Bitte geben Sie ein längeres Passwort ein.'
        );
        $this->setLocalization(
            'ger',
            'login',
            'changepasswordSuccess',
            'Sie haben Ihr Passwort erfolgreich geändert.'
        );
        $this->setLocalization(
            'ger',
            'login',
            'changepasswordWrongPass',
            'Das angegebene alte Passwort ist nicht korrekt. Bitte geben Sie es erneut ein.'
        );
        $this->setLocalization(
            'ger',
            'login',
            'dataEditSuccessful',
            'Die Daten wurden erfolgreich aktualisiert.'
        );
        $this->setLocalization(
            'ger',
            'login',
            'kwkEmail',
            'E-Mail-Adresse'
        );
        $this->setLocalization(
            'ger',
            'login',
            'kwkName',
            'Werben Sie einen Freund!'
        );
        $this->setLocalization(
            'ger',
            'login',
            'newPasswordRpt',
            'Neues Passwort (Wdh.)'
        );
        $this->setLocalization(
            'ger',
            'login',
            'notPayedYet',
            'Noch kein Zahlungseingang'
        );
        $this->setLocalization(
            'ger',
            'login',
            'notShippedYet',
            'Noch nicht versendet'
        );
        $this->setLocalization(
            'ger',
            'login',
            'orderNo',
            'Bestellnummer'
        );
        $this->setLocalization(
            'ger',
            'login',
            'passwordhasUsername',
            'Das Passwort darf den Namen des Benutzers nicht enthalten.'
        );
        $this->setLocalization(
            'ger',
            'login',
            'passwordIsMedium',
            'Mittel; versuchen Sie, Sonderzeichen zu verwenden.'
        );
        $this->setLocalization(
            'ger',
            'login',
            'reallyDeleteAccount',
            'Möchten Sie Ihr Kundenkonto wirklich unwiderruflich löschen?'
        );
        $this->setLocalization(
            'ger',
            'login',
            'trackingId',
            'Sendungsverfolgungsnummer'
        );
        $this->setLocalization(
            'ger',
            'login',
            'typeYourPassword',
            'Bitte geben Sie ein Passwort ein.'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistEmailCount',
            'Maximale Anzahl der E-Mail-Empfänger'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistEmails',
            'E-Mail-Empfänger – getrennt durch Leerzeichen'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistNotPrivat',
            'Wunschzettel auf „Öffentlich“ setzen'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistPrivat',
            'Wunschzettel auf „Privat“ setzen'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistPublic',
            'Öffentlich'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistremoveItem',
            'Artikel entfernen'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistRemoveSearch',
            'Suche löschen'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistSaveNew',
            'Erstellen'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistSetPrivate',
            'Auf „Privat“ setzen'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistSetPublic',
            'Auf „Öffentlich“ setzen'
        );
        $this->setLocalization(
            'ger',
            'login',
            'wishlistURL',
            'Link zu Ihrem Wunschzettel:'
        );
        $this->setLocalization(
            'ger',
            'forgot password',
            'createNewPassword',
            'Neues Passwort anfordern'
        );
        $this->setLocalization(
            'ger',
            'forgot password',
            'forgotPasswordDesc',
            'Bitte geben Sie hier die E-Mail-Adresse ein, mit der Sie sich registriert haben.'
        );
        $this->setLocalization(
            'ger',
            'contact',
            'youSentUsAMessageShortTimeBefore',
            'Sie haben uns vor kurzer Zeit bereits eine Nachricht geschickt.'
            . ' Bitte warten Sie einen Moment, bevor Sie uns erneut eine Nachricht schicken.'
        );
        $this->setLocalization(
            'ger',
            'comparelist',
            'addtowishlist',
            'Auf den Wunschzettel'
        );
        $this->setLocalization(
            'ger',
            'comparelist',
            'back',
            'Zurück zum Onlineshop'
        );
        $this->setLocalization(
            'ger',
            'comparelist',
            'productNumberHint',
            'Bitte fügen Sie mindestens zwei Artikel zur Vergleichsliste hinzu.'
        );
        $this->setLocalization(
            'ger',
            'comparelist',
            'weightUnit',
            'kg'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'allreadyWroteReview',
            'Sie haben bereits eine Bewertung zu diesem Artikel abgegeben.'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'feedback activated',
            'Bewertung ist freigeschaltet.'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'feedback deactivated',
            'Bewertung ist noch nicht freigeschaltet.'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'isRatingHelpful',
            'Ist diese Bewertung hilfreich für Sie?'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'latestReviewFirst',
            'Älteste zuerst'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'maxProduct1',
            'Es liegen'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'shareYourExperience',
            'Teilen Sie anderen Kunden Ihre Erfahrungen mit!'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'shareYourRatingGuidelines',
            'Teilen Sie uns Ihre Meinung mit!'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'unusefulClassifiedReviewFirst',
            'Als „Nicht hilfreich“ eingestufte zuerst'
        );
        $this->setLocalization(
            'ger',
            'product rating',
            'usefulClassifiedReviewFirst',
            'Als „Hilfreich“ eingestufte zuerst'
        );
        $this->setLocalization(
            'ger',
            'newsletter',
            'newsletterhistoryback',
            'Zurück'
        );
        $this->setLocalization(
            'ger',
            'newsletter',
            'newsletterHtml',
            'Inhalt (HTML)'
        );
        $this->setLocalization(
            'ger',
            'newsletter',
            'newsletterSendSubscribe',
            'Abonnieren'
        );
        $this->setLocalization(
            'ger',
            'newsletter',
            'newsletterSendUnsubscribe',
            'Abmelden'
        );
        $this->setLocalization(
            'ger',
            'newsletter',
            'newsletterSubscribeDesc',
            'Um sich für unseren Newsletter anzumelden, tragen Sie bitte hier Ihre E-Mail-Adresse ein '
            . 'und klicken anschließend auf „Abonnieren“.'
        );
        $this->setLocalization(
            'ger',
            'newsletter',
            'newsletterUnsubscribeDesc',
            'Um sich vom Newsletter abzumelden, tragen Sie bitte Ihre E-Mail-Adresse in das untenstehende '
            . 'Feld ein. Klicken Sie anschließend auf „Abmelden“.'
        );
        $this->setLocalization(
            'ger',
            'newsletter',
            'unsubscribeAnytime',
            'Abonnieren Sie jetzt den Newsletter und verpassen Sie keine Angebote. Die Abmeldung ist jederzeit möglich.'
        );
        $this->setLocalization(
            'ger',
            'news',
            'monthOverview',
            'News: Monate mit Beiträgen'
        );
        $this->setLocalization(
            'ger',
            'news',
            'moreLink',
            'Weiter'
        );
        $this->setLocalization(
            'ger',
            'news',
            'newsCat',
            'Kategorie des News-Systems'
        );
        $this->setLocalization(
            'ger',
            'news',
            'newsCatOverview',
            'Übersicht der Kategorien des News-Systems'
        );
        $this->setLocalization(
            'ger',
            'news',
            'newsCats',
            'Kategorien des News-Systems'
        );
        $this->setLocalization(
            'ger',
            'news',
            'newsCommentSave',
            'Speichern'
        );
        $this->setLocalization(
            'ger',
            'news',
            'newsMonthOverview',
            'News: Monate mit Beiträgen'
        );
        $this->setLocalization(
            'ger',
            'news',
            'newsSortCommentsASC',
            'Kommentarzahl aufsteigend'
        );
        $this->setLocalization(
            'ger',
            'news',
            'newsSortCommentsDESC',
            'Kommentarzahl absteigend'
        );
        $this->setLocalization(
            'ger',
            'news',
            'newsSortHeadlineASC',
            'Überschrift A–Z'
        );
        $this->setLocalization(
            'ger',
            'news',
            'newsSortHeadlineDESC',
            'Überschrift Z–A'
        );
        $this->setLocalization(
            'ger',
            'news',
            'noNewsArchiv',
            'Leider befinden sich noch keine News-Beiträge im Archiv.'
        );
        $this->setLocalization(
            'ger',
            'news',
            'overview',
            'News-Übersicht'
        );
        $this->setLocalization(
            'ger',
            'strength',
            'stronger',
            'sehr stark'
        );
        $this->setLocalization(
            'ger',
            'breadcrumb',
            'bcWishlist',
            'Wunschzettel'
        );
        $this->setLocalization(
            'ger',
            'breadcrumb',
            'login',
            'Anmeldung'
        );
        $this->setLocalization(
            'ger',
            'breadcrumb',
            'newskat',
            'Kategorie des News-Systems'
        );
        $this->setLocalization(
            'ger',
            'breadcrumb',
            'newsmonat',
            'News-Monat'
        );
        $this->setLocalization(
            'ger',
            'breadcrumb',
            'wishlist',
            'Wunschzettel'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'noShippingCostsAtExtended',
            'nach %s.'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'noShippingCostsReached',
            'Ihre Bestellung ist mit %s versandkostenfrei %s lieferbar.'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'orderExpandInventory',
            'Folgende Artikel sind in der gewählten Menge nicht verfügbar und die Lieferung '
            . 'könnte sich aufgrund dessen verzögern: %s'
        );
        $this->setLocalization(
            'ger',
            'order',
            'openPartialShipped',
            'Offene Teillieferungen'
        );
        $this->setLocalization(
            'ger',
            'order',
            'partialShipped',
            'Teillieferung'
        );
        $this->setLocalization(
            'ger',
            'order',
            'statusPaid',
            'Bezahlt'
        );
        $this->setLocalization(
            'ger',
            'order',
            'statusPartialShipped',
            'Teilgeliefert'
        );
        $this->setLocalization(
            'ger',
            'order',
            'statusPending',
            'Offen'
        );
        $this->setLocalization(
            'ger',
            'order',
            'statusProcessing',
            'In Bearbeitung'
        );
        $this->setLocalization(
            'ger',
            'order',
            'statusShipped',
            'Versendet'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'accountDeleted',
            'Das Kundenkonto wurde erfolgreich gelöscht.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'artikelVariBoxEmpty',
            'Bitte geben Sie mindestens die Artikelmenge in der Variationsbox ein.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'availAgainOptinCreated',
            'Vielen Dank, wir haben Ihre Daten erhalten. Wir haben Ihnen eine E-Mail mit einem '
            . 'Freischaltcode zugeschickt. Bitte klicken Sie auf den Link in der E-Mail, um informiert '
            . 'zu werden, sobald der Artikel wieder verfügbar ist.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'basketAjaxAdded',
            'Der Artikel %s wurde Ihrem Warenkorb hinzugefügt. (%dx)'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'bewertungBewaddacitvate',
            'Ihre Bewertung wurde eingetragen und muss nun vom Verkäufer freigeschaltet werden.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'bewertungBewaddCredits',
            'Ihre Bewertung wurde eingetragen und Ihnen wurde %s € auf Ihr Kundenkonto gutgeschrieben.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'chooseVariations',
            'Dieser Artikel hat Variationen. Wählen Sie bitte die gewünschte Variation aus.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'comparelistProductadded',
            'Ihr ausgewählter Artikel wurde der Vergleichsliste hinzugefügt.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'continueAfterActivation',
            'Sie können mit dem Bestellprozess fortfahren, sobald Ihr Kundenkonto freigeschaltet wurde.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'loginWishlist',
            'Bitte melden Sie sich an, um dem Wunschzettel Artikel hinzuzufügen.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'maxTagsExceeded',
            'Entschuldigung, Sie haben bereits die innerhalb von 24 Stunden erlaubte '
            . 'Anzahl an Artikel-Tags hinzugefügt.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'newscommentAddactivate',
            'Ihr Kommentar wurde erfolgreich gespeichert und muss nun vom Verkäufer freigeschaltet werden.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'newsletterAdd',
            'Vielen Dank, wir haben Ihre Daten erhalten. Wir haben Ihnen eine E-Mail mit einem '
            . 'Freischalt-Code zugeschickt. Bitte klicken Sie auf den Link in der E-Mail, um Ihre '
            . 'Anmeldung zum Newsletter erfolgreich abzuschließen.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'newsletterNomailAdd',
            'Vielen Dank, Sie wurden in die Verteilerliste für den Newsletter-Versand eingetragen.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'nowlidWishlist',
            'Der Wunschzettel mit der ID „%s“ ist entweder nicht mehr öffentlich oder wurde gelöscht.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'optinCanceled',
            'Ihre Einwilligung wurde aufgehoben.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'optinRemoved',
            'Ihr Freischaltantrag wurde gelöscht.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'optinSucceededMailSent',
            'Es wurde bereits eine E-Mail mit Ihrem Freischalt-Code an Sie verschickt.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'pleaseLogin',
            'Bitte melden Sie sich an, um in unserem Onlineshop einzukaufen.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'pleaseLoginToAddTags',
            'Bitte melden Sie sich an, um Artikel-Tags hinzuzufügen.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'pollCoupon',
            'Vielen Dank für die Teilnahme an unserer Umfrage. Für Ihre nächste Bestellung steht Ihnen '
            . 'der folgende Coupon-Code zur Verfügung: %s.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'pollCredit',
            'Vielen Dank für die Teilnahme an unserer Umfrage. Ihnen wurde ein Guthaben von %s gutgeschrieben.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'pollError',
            'Bei der Auswertung ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'preorderNotPossible',
            'Eine Vorbestellung dieses Artikels ist leider nicht möglich.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'quantityNotAvailable',
            'Die gewünschte Artikelmenge ist leider nicht verfügbar. Bitte geben Sie eine kleinere Menge an.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'quantityNotAvailableVar',
            'Die gewünschte Artikelmenge ist in der gewählten Variation leider nicht verfügbar. '
            . 'Bitte geben Sie eine kleinere Menge an oder wählen Sie eine andere Variation aus.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'questionNotPossible',
            'Sie haben erst kürzlich eine Frage zum Artikel abgeschickt. Bitte warten Sie einige Zeit, '
            . 'um eine neue Frage zum Artikel zu versenden.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'tagAccepted',
            'Der Artikel-Tag wurde hinzugefügt.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'tagAcceptedWaitCheck',
            'Ihr Artkel-Tag wurde hinzugefügt und wird vom Administrator geprüft.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'tagArtikelEmpty',
            'Bitte geben Sie einen Namen für den Artikel-Tag ein.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'thankYouForNotificationSubscription',
            'Vielen Dank. Wir werden Sie umgehend benachrichtigen, sobald der Artikel wieder verfügbar ist.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'thankYouForQuestion',
            'Vielen Dank für Ihre Frage zum Artikel.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'wishlistProductadded',
            'Der ausgewählte Artikel wurde zu Ihrem Wunschzettel hinzugefügt.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'wkMaxorderlimit',
            'Die Bestellmenge für diesen Artikel ist leider zu hoch.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'wkOnrequest',
            'Artikelpreis auf Anfrage'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'wkPurchaseintervall',
            'Die Bestellmenge für diesen Artikel muss ein Vielfaches des Abnahmeintervalls sein.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'bewertungBewexist',
            'Sie haben bereits eine Bewertung zu diesem Artikel abgegeben.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'cartPersRemoved',
            'Der Artikel „%s“ konnte nicht in den Warenkorb übernommen werden.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'compareMaxlimit',
            'Entschuldigung, Ihre Vergleichsliste hat die maximale Anzahl an Artikeln erreicht. '
            . 'Bitte entfernen Sie Artikel von der Vergleichsliste, um neue Artikel hinzuzufügen. '
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'freegiftsMinimum',
            'Der für das Gratisgeschenk benötigte Mindestbestellwert ist noch nicht erreicht.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'kwkAlreadyreg',
            'Die E-Mail-Adresse %s wird bereits verwendet.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'kwkEmailblocked',
            'Diese E-Mail-Adresse ist gesperrt.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'kwkWrongdata',
            'Bitte geben Sie gültige Daten ein.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'mandatoryFieldNotification',
            'Bitte füllen Sie alle Pflichtfelder aus.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'missingParamShippingDetermination',
            'Bitte füllen Sie die Angaben zu Land und Postleitzahl korrekt aus.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'missingTaxZoneForDeliveryCountry',
            'Ein Versand nach %s ist aktuell nicht möglich, da keine gültige Steuerzone hinterlegt ist.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newscommentAlreadywritten',
            'Sie haben bereits die maximale Anzahl an Kommentaren zu diesem News-Beitrag erreicht.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newscommentLongtext',
            'Sie haben die maximale Zeichenzahl von 1000 Zeichen für den Kommentartext überschritten.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newscommentMissingnameemail',
            'Bitte geben Sie einen Namen sowie eine E-Mail-Adresse ein.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newscommentMissingtext',
            'Bitte geben Sie einen Text ein.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newsletterCaptcha',
            'Bitte beachten Sie das Captcha.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newsletterNoactive',
            'Der Freischalt-Code wurde nicht gefunden.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newsletterNocode',
            'Ihr Lösch-Code konnte nicht in der Datenbank gefunden werden. '
            . 'Bitte überprüfen Sie die Eingabe auf eventuelle Tippfehler oder wenden Sie sich ggf. an den Support.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newsletterNoexists',
            'Ihre E-Mail-Adresse ist nicht in der Datenbank vorhanden. '
            . 'Bitte überprüfen Sie die Eingabe auf eventuelle Tippfehler oder wenden Sie sich ggf. an den Support.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newsletterNoname',
            'Bitte geben Sie einen Vor- und Nachnamen ein.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newsletterWrongemail',
            'Entschuldigung, Ihre E-Mail-Adresse weist ein ungültiges Format auf.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'noCookieDesc',
            'Zur Nutzung unserer Seite müssen Sie im Browser Cookies aktivieren.<br />'
            . 'Rufen Sie dann noch einmal unsere <a href="index.php">Startseite</a> auf.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'noCookieHeader',
            'Cookies aktivieren'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'noMediaFile',
            'Es sind keine Mediendateien vorhanden.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'optinActionUnknown',
            'Unbekannte Aktion angefordert. Bitte wenden Sie sich an den Support.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'optinCodeUnknown',
            'Der eingegebene Bestätigungscode ist nicht bekannt. '
            . 'Bitte überprüfen Sie die Eingabe auf eventuelle Tippfehler oder wenden Sie sich ggf. an den Support.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'pollAlreadydid',
            'Entschuldigung, Sie haben bereits an dieser Umfrage teilgenommen.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'pollRequired',
            'Bitte geben Sie alle erforderlichen Antworten.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'productquestionPleaseLogin',
            'Entschuldigung, Sie müssen angemeldet sein, um eine Frage zum Artikel zu versenden.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'ratingRange',
            'Die Bewertung muss eine Zahl von 1 bis 5 sein.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'statusOrderNotFound',
            'Es wurde keine passende Bestellung gefunden.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'uidNotFound',
            'Es wurde keine Bestellung zu der angeforderten ID gefunden.'
        );
        $this->setLocalization(
            'ger',
            'redirect',
            'tag',
            'Artikel-Tag'
        );
        $this->setLocalization(
            'ger',
            'redirect',
            'wishlist',
            'Wunschzettel'
        );
        $this->setLocalization(
            'ger',
            'paymentMethods',
            'errorMailBody',
            'In Ihrem Onlineshop %s ist bei Bestellung %s beim Initialisieren des Bezahlvorgangs '
            . 'mit der Zahlungsart %s folgender Fehler aufgetreten: %s.'
        );
        $this->setLocalization(
            'ger',
            'paymentMethods',
            'errorMailSubject',
            'Fehler in Ihrem Onlineshop %s'
        );
        $this->setLocalization(
            'ger',
            'paymentMethods',
            'errorText',
            'Beim Initialisieren des Bezahlvorgangs ist ein Fehler aufgetreten. Der Onlineshop-Betreiber '
            . 'wurde benachrichtigt und wird sich mit Ihnen in Verbindung setzen.'
        );
        $this->setLocalization(
            'ger',
            'paymentMethods',
            'paypalError',
            'Bei der Kommunikation mit PayPal ist ein Fehler aufgetreten. Möglicherweise ist Ihr PayPal-Konto '
            . 'noch nicht freigeschaltet. Falls das nicht der Fall ist, wenden Sie sich bitte mit folgendem '
            . 'Fehlercode an den Betreiber des Onlineshops: %s'
        );
        $this->setLocalization(
            'ger',
            'paymentMethods',
            'paypalText',
            'Bezahlen Sie Ihre Bestellung %s von %s.'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_error_alreadysend',
            'Sie haben bereits zu einigen ausgewählten Artikeln eine Rücksendenummer (RMA) abgesendet.'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_error_noarticle',
            'Bitte markieren Sie mindestens einen Artikel.'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_error_validquantity',
            'Vergewissern Sie sich, dass Sie eine Artikelmenge angegeben haben und diese nicht die '
            . 'bestellte Menge überschreitet.'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_go',
            'Los'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_info_success',
            'Die Warenrücksendung mit Nummer %s wurde erfolgreich gespeichert.'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_login',
            'Bitte melden Sie sich an, um eine Warenrücksendung durchzuführen.'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_nolicence',
            'Es wurde keine gültige Lizenz für dieses Modul gefunden.'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_number',
            'Rücksendenummer'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_success_msg_1',
            'Vielen Dank, dass Sie unsere Warenrücksendung nutzen.<br /><br />'
            . 'Im Folgenden finden Sie eine Übersicht über Ihre zur Rücksendung ausgewählten Artikel.'
        );
        $this->setLocalization(
            'ger',
            'rma',
            'rma_success_msg_2',
            'Bitte drucken Sie diese aus und legen sie Ihrem Rücksendepaket bei.<br /><br />'
            . 'Sobald wir Ihre Rücksendung verarbeitet haben, benachrichtigen wir Sie per E-Mail über die '
            . 'Erstattung (sofern zutreffend).<br /><br />Vielen Dank für Ihren Einkauf bei'
        );
        $this->setLocalization(
            'ger',
            'wishlist',
            'setAsStandardWishlist',
            'Lassen Sie neue Artikel standardmäßig auf den aktuell gewählten Wunschzettel setzen.'
        );
        $this->setLocalization(
            'ger',
            'wishlist',
            'wlDelete',
            'Wunschzettel löschen'
        );

        $this->setLocalization(
            'eng',
            'global',
            'aaSelectBTN',
            'Select'
        );
        $this->setLocalization(
            'eng',
            'global',
            'accountCreated',
            'Your customer account has been created.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'accountDeleteFailure',
            'You customer account cannot be delete. There is still an open order.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'accountInactive',
            'Your customer account has been deactivated.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'accountLocked',
            'Your customer account has been locked.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'activateAccountDesc',
            'We will check your data and activate your customer account as soon as possible.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'additionalCharge',
            'Surcharge'
        );
        $this->setLocalization(
            'eng',
            'global',
            'adminMaintenanceMode',
            'The Maintenance mode of the online shop is active. Since you are logged on as an administrator, '
            . 'you can still use all the features of the online shop.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'adrHazardSign',
            'European ADR hazard sign'
        );
        $this->setLocalization(
            'eng',
            'global',
            'agb',
            'Terms and conditions'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ajaxcheckoutChangemethode',
            'Edit'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ajaxLoading',
            'Loading…'
        );
        $this->setLocalization(
            'eng',
            'global',
            'allDates',
            'Every date'
        );
        $this->setLocalization(
            'eng',
            'global',
            'AllProductsPerSite',
            'All items'
        );
        $this->setLocalization(
            'eng',
            'global',
            'allRatings',
            'All reviews'
        );
        $this->setLocalization(
            'eng',
            'global',
            'alreadyCustomer',
            'I am already a customer.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ampelGelb',
            'Low stock level'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ampelGruen',
            'Available immediately'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ampelRot',
            'Currently out of stock'
        );
        $this->setLocalization(
            'eng',
            'global',
            'asc',
            'in ascending order'
        );
        $this->setLocalization(
            'eng',
            'global',
            'basketCustomerWhoBoughtXBoughtAlsoY',
            'Others also bought'
        );
        $this->setLocalization(
            'eng',
            'global',
            'bestseller',
            'Bestsellers'
        );
        $this->setLocalization(
            'eng',
            'global',
            'bestsellers',
            'Our bestsellers'
        );
        $this->setLocalization(
            'eng',
            'global',
            'blockedEmail',
            'The email address is blocked!'
        );
        $this->setLocalization(
            'eng',
            'global',
            'BoxPoll',
            'Surveys'
        );
        $this->setLocalization(
            'eng',
            'global',
            'captchaMathQuestion',
            'What is'
        );
        $this->setLocalization(
            'eng',
            'global',
            'categoryoverviewSub',
            'Select a category!'
        );
        $this->setLocalization(
            'eng',
            'global',
            'change',
            'Edit'
        );
        $this->setLocalization(
            'eng',
            'global',
            'characters',
            'characters.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'charge',
            'Batch'
        );
        $this->setLocalization(
            'eng',
            'global',
            'compare',
            'Comparison list'
        );
        $this->setLocalization(
            'eng',
            'global',
            'configure',
            'Configure'
        );
        $this->setLocalization(
            'eng',
            'global',
            'contact',
            'Contact data'
        );
        $this->setLocalization(
            'eng',
            'global',
            'copied',
            'copied'
        );
        $this->setLocalization(
            'eng',
            'global',
            'copyright',
            '© 2019'
        );
        $this->setLocalization(
            'eng',
            'global',
            'copyrightName',
            'JTL-Shop'
        );
        $this->setLocalization(
            'eng',
            'global',
            'counter',
            'Visitor counter'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr1',
            'Coupon not active.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr10',
            'The coupon is invalid for the entered destination.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr12',
            'This coupon is invalid for the current basket (only valid for certain manufacturers).'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr2',
            'The coupon is no longer valid (date expired).'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr3',
            'Coupon is no longer valid.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr4',
            'The minimum order value required for this coupon has not yet been reached.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr5',
            'The coupon is invalid for the current customer group.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr6',
            'The maximum allowed number of claims for this coupon has been reached.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr7',
            'This coupon is invalid for the current basket (only valid for certain items).'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr8',
            'This coupon is invalid for the current basket (only valid for certain categories).'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr9',
            'The coupon is invalid for your customer account.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr99',
            'Unknown coupon error. Please enter the data again or contact the Support team, if required.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponSucc1',
            'Your free shipping coupon has been activated for the following shipping countries:'
        );
        $this->setLocalization(
            'eng',
            'global',
            'currently',
            'Current'
        );
        $this->setLocalization(
            'eng',
            'global',
            'customerInformation',
            'Customer data'
        );
        $this->setLocalization(
            'eng',
            'global',
            'dateOfIssue',
            'Estimated release date:'
        );
        $this->setLocalization(
            'eng',
            'global',
            'delete',
            'Remove'
        );
        $this->setLocalization(
            'eng',
            'global',
            'deliveryCountry',
            'Destination country'
        );
        $this->setLocalization(
            'eng',
            'global',
            'deliverytimeEstimation',
            '#MINDELIVERYDAYS# - #MAXDELIVERYDAYS# Workdays'
        );
        $this->setLocalization(
            'eng',
            'global',
            'deliverytimeEstimationSimple',
            '#DELIVERYDAYS# Workdays'
        );
        $this->setLocalization(
            'eng',
            'global',
            'details',
            'Go to item'
        );
        $this->setLocalization(
            'eng',
            'global',
            'differentialPrice',
            'Price range'
        );
        $this->setLocalization(
            'eng',
            'global',
            'dlErrorCustomerNotMatch',
            'The logged-in costumer and the download permission for the file do not match.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'dlErrorDownloadLimitReached',
            'The maximum download limit has been reached.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'dlErrorDownloadNotFound',
            'An item with this download does not exist.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'dlErrorOrderNotFound',
            'Your order could not be found.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'dlErrorValidityReached',
            'The validity period has been exceeded.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'dlErrorWrongParameter',
            'Invalid parameters. There is no file that matches this download link.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'downloadPending',
            'Payment pending'
        );
        $this->setLocalization(
            'eng',
            'global',
            'dse',
            'Privacy statement'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ean',
            'GTIN'
        );
        $this->setLocalization(
            'eng',
            'global',
            'eanNotExist',
            'Unfortunately our assortment does not contain any item with the following SKU/GTIN:'
        );
        $this->setLocalization(
            'eng',
            'global',
            'else',
            'otherwise'
        );
        $this->setLocalization(
            'eng',
            'global',
            'emailadress',
            'Email address'
        );
        $this->setLocalization(
            'eng',
            'global',
            'enterResult',
            'Enter displayed text'
        );
        $this->setLocalization(
            'eng',
            'global',
            'estimateShippingCostsNote',
            'The shipping costs can only be calculated once the item is in the basket.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ExemptFromCharges',
            'free of charge'
        );
        $this->setLocalization(
            'eng',
            'global',
            'expressionHasTo',
            'The search term must at least consist of'
        );
        $this->setLocalization(
            'eng',
            'global',
            'extendedSearch',
            'Advanced search'
        );
        $this->setLocalization(
            'eng',
            'global',
            'fillOut',
            'Please complete!'
        );
        $this->setLocalization(
            'eng',
            'global',
            'filterAndSort',
            'Filters and sort order'
        );
        $this->setLocalization(
            'eng',
            'global',
            'financingIncludesProcessingFee',
            'includes handling fee'
        );
        $this->setLocalization(
            'eng',
            'global',
            'find',
            'Find'
        );
        $this->setLocalization(
            'eng',
            'global',
            'findProduct',
            'Find item'
        );
        $this->setLocalization(
            'eng',
            'global',
            'firstReview',
            'Write the first review for this item and help others make a purchase decision!'
        );
        $this->setLocalization(
            'eng',
            'global',
            'footnoteExclusiveShipping',
            ', plus <a href="%s">shipping fees</a>'
        );
        $this->setLocalization(
            'eng',
            'global',
            'footnoteExclusiveVat',
            'All prices plus VAT'
        );
        $this->setLocalization(
            'eng',
            'global',
            'footnoteInclusiveShipping',
            ', incl. <a href="%s">shipping fees</a>'
        );
        $this->setLocalization(
            'eng',
            'global',
            'footnoteInclusiveVat',
            'All prices incl. VAT'
        );
        $this->setLocalization(
            'eng',
            'global',
            'forgotPassword',
            'Forgot password'
        );
        $this->setLocalization(
            'eng',
            'global',
            'found',
            'found'
        );
        $this->setLocalization(
            'eng',
            'global',
            'freeGiftFrom1',
            'For orders from'
        );
        $this->setLocalization(
            'eng',
            'global',
            'freeGiftFromOrderValue',
            'In the basket you can choose from the following free gifts, provided that the value of items '
            . 'in your basket matches the required value.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'freeGiftFromOrderValueBasket',
            'Choose a free gift:'
        );
        $this->setLocalization(
            'eng',
            'global',
            'freeshipping',
            'eligible for free shipping'
        );
        $this->setLocalization(
            'eng',
            'global',
            'gotoBasket',
            'Go to basket'
        );
        $this->setLocalization(
            'eng',
            'global',
            'goTop',
            'Go to top'
        );
        $this->setLocalization(
            'eng',
            'global',
            'goToWishlist',
            'Go to wish list'
        );
        $this->setLocalization(
            'eng',
            'global',
            'gotToCompare',
            'Compare items'
        );
        $this->setLocalization(
            'eng',
            'global',
            'hours',
            'hours'
        );
        $this->setLocalization(
            'eng',
            'global',
            'incorrectEmail',
            'There is no customer with the specified email address. Please try again.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'incorrectEmailPlz',
            'There is no customer with the specified email address and postal code. Please try again.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'incorrectLogin',
            'User name and password do not match. Please try again.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'insteadOf',
            'Previous price'
        );
        $this->setLocalization(
            'eng',
            'global',
            'invalidDateformat',
            'Enter the date in the format DD.MM.YYYY, e.g. 04.11.1981.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'invalidEmail',
            'Please enter a valid email address.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'invalidInteger',
            'Please enter a number.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'invalidResult',
            'Invalid entry'
        );
        $this->setLocalization(
            'eng',
            'global',
            'invalidTel',
            'Please enter your number in digits only.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'invalidToken',
            'The entered security code is invalid.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'invalidURL',
            'Please enter a valid URL.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'lastaddeditems',
            'Recently added items'
        );
        $this->setLocalization(
            'eng',
            'global',
            'lastsearch',
            'Recent search queries'
        );
        $this->setLocalization(
            'eng',
            'global',
            'lastViewed',
            'Recently viewed'
        );
        $this->setLocalization(
            'eng',
            'global',
            'leaveMobileView',
            'Exit mobile view'
        );
        $this->setLocalization(
            'eng',
            'global',
            'listOfItems',
            'This item consists of'
        );
        $this->setLocalization(
            'eng',
            'global',
            'loggedOut',
            'You have been logged off successfully.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'login',
            'Log in'
        );
        $this->setLocalization(
            'eng',
            'global',
            'loginBasket',
            'Log in'
        );
        $this->setLocalization(
            'eng',
            'global',
            'loginNotActivated',
            'Your customer account has not yet been activated. Please try again later.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'logOut',
            'Log out'
        );
        $this->setLocalization(
            'eng',
            'global',
            'lookAtTop',
            'see above'
        );
        $this->setLocalization(
            'eng',
            'global',
            'maintenanceModeActive',
            'This online shop is currently in Maintenance mode.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'matches',
            'Results'
        );
        $this->setLocalization(
            'eng',
            'global',
            'maxUploadSize',
            'The maximum size per file is'
        );
        $this->setLocalization(
            'eng',
            'global',
            'mdh',
            'Shelf life expiration date'
        );
        $this->setLocalization(
            'eng',
            'global',
            'minutes',
            'minutes'
        );
        $this->setLocalization(
            'eng',
            'global',
            'miscellaneous',
            'Misc.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'modifyBillingAdress',
            'Edit billing address'
        );
        $this->setLocalization(
            'eng',
            'global',
            'modifyBillingAdressSimple',
            'Edit'
        );
        $this->setLocalization(
            'eng',
            'global',
            'monthlyPer',
            'monthly'
        );
        $this->setLocalization(
            'eng',
            'global',
            'morevariations',
            'Select more variations'
        );
        $this->setLocalization(
            'eng',
            'global',
            'mr',
            'Mr'
        );
        $this->setLocalization(
            'eng',
            'global',
            'mrs',
            'Ms'
        );
        $this->setLocalization(
            'eng',
            'global',
            'myCompareList',
            'My comparison list'
        );
        $this->setLocalization(
            'eng',
            'global',
            'myDownloads',
            'My downloads'
        );
        $this->setLocalization(
            'eng',
            'global',
            'myWishlists',
            'My wish lists'
        );
        $this->setLocalization(
            'eng',
            'global',
            'newestFirst',
            'Newest first'
        );
        $this->setLocalization(
            'eng',
            'global',
            'newHere',
            'New to our online shop?'
        );
        $this->setLocalization(
            'eng',
            'global',
            'newProducts',
            'New in product range'
        );
        $this->setLocalization(
            'eng',
            'global',
            'newsArchivDesc',
            'All news postings in archive '
        );
        $this->setLocalization(
            'eng',
            'global',
            'newsBoxCatOverview',
            'News: categories of the news system'
        );
        $this->setLocalization(
            'eng',
            'global',
            'newsBoxMonthOverview',
            'News: months with news postings'
        );
        $this->setLocalization(
            'eng',
            'global',
            'newsletterhistory',
            'Newsletter archive'
        );
        $this->setLocalization(
            'eng',
            'global',
            'newsletterhistorydate',
            'Shipping date'
        );
        $this->setLocalization(
            'eng',
            'global',
            'noDispatchAvailable',
            'Unfortunately, there is no shipping method available.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'noEntriesAvailable',
            'No entries available.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'noSalutation',
            'No title'
        );
        $this->setLocalization(
            'eng',
            'global',
            'noShippingcostsTo',
            'Free shipping'
        );
        $this->setLocalization(
            'eng',
            'global',
            'notAvailableInSelection',
            'Unfortunately, your selection is not available.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'notifyMeWhenProductAvailableAgain',
            'Notify me when this item is available'
        );
        $this->setLocalization(
            'eng',
            'global',
            'notOnComparelist',
            'Not on comparison list'
        );
        $this->setLocalization(
            'eng',
            'global',
            'notOnWishlist',
            'Not on wish list'
        );
        $this->setLocalization(
            'eng',
            'global',
            'nowOnly',
            'only'
        );
        $this->setLocalization(
            'eng',
            'global',
            'onComparelist',
            'On comparison list'
        );
        $this->setLocalization(
            'eng',
            'global',
            'onWishlist',
            'On wish list'
        );
        $this->setLocalization(
            'eng',
            'global',
            'order',
            'Order'
        );
        $this->setLocalization(
            'eng',
            'global',
            'pageNotFound',
            'Page not found.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'paginationEntriesPerPage',
            'Entries per page'
        );
        $this->setLocalization(
            'eng',
            'global',
            'paginationOrderByRating',
            'Review'
        );
        $this->setLocalization(
            'eng',
            'global',
            'paginationOrderUsefulness',
            'Helpful'
        );
        $this->setLocalization(
            'eng',
            'global',
            'paymentOptions',
            'Payment method'
        );
        $this->setLocalization(
            'eng',
            'global',
            'payWithPaypal',
            'Pay now with PayPal'
        );
        $this->setLocalization(
            'eng',
            'global',
            'position',
            'Position'
        );
        $this->setLocalization(
            'eng',
            'global',
            'preferredDeliveryAddress',
            'Preferred shipping address'
        );
        $this->setLocalization(
            'eng',
            'global',
            'preorderPossible',
            'Pre-orders possible'
        );
        $this->setLocalization(
            'eng',
            'global',
            'priceOnApplication',
            'Price on request'
        );
        $this->setLocalization(
            'eng',
            'global',
            'priceRadar',
            'Price radar'
        );
        $this->setLocalization(
            'eng',
            'global',
            'priceStarting',
            'from'
        );
        $this->setLocalization(
            'eng',
            'global',
            'private',
            'Private'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productAddedToCart',
            'successfully added to basket.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productAvailable',
            'Available'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productAvailableFrom',
            'Available from'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productExtraShippingNotice',
            'Additional shipping costs of %s apply.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productMHD',
            'Shelf life'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productMHDTool',
            'Shelf life expiration date'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productNo',
            'SKU'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productNoEAN',
            'SKU/GTIN'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productNoExtraShippingNotice',
            'No additional shipping costs apply.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productNotAvailable',
            'Item out of stock'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productsFrom',
            'Items by'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productsTaggedAs',
            'Items with tags'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productsWith',
            'Item with'
        );
        $this->setLocalization(
            'eng',
            'global',
            'productWeight',
            'Item weight'
        );
        $this->setLocalization(
            'eng',
            'global',
            'public',
            'Public'
        );
        $this->setLocalization(
            'eng',
            'global',
            'quantity',
            'Quantity'
        );
        $this->setLocalization(
            'eng',
            'global',
            'questiontoproduct',
            'Question about:'
        );
        $this->setLocalization(
            'eng',
            'global',
            'questiontoproductSuccessful',
            'Your question has been submitted successfully. You will shortly send you an email.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'rangeOfPrices',
            'Price range'
        );
        $this->setLocalization(
            'eng',
            'global',
            'rating',
            'Reviews'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ratingAverage',
            'Review'
        );
        $this->setLocalization(
            'eng',
            'global',
            'redirect',
            'You will now be redirected…'
        );
        $this->setLocalization(
            'eng',
            'global',
            'redirectDesc1',
            'You will then be returned to'
        );
        $this->setLocalization(
            'eng',
            'global',
            'redirectDesc2',
            'page'
        );
        $this->setLocalization(
            'eng',
            'global',
            'registerBasket',
            'You are a new customer?'
        );
        $this->setLocalization(
            'eng',
            'global',
            'registerNow',
            'Register now!'
        );
        $this->setLocalization(
            'eng',
            'global',
            'removeFilters',
            'Reset all filters'
        );
        $this->setLocalization(
            'eng',
            'global',
            'requestNotification',
            'Request notification'
        );
        $this->setLocalization(
            'eng',
            'global',
            'resetSelection',
            'Reset selection'
        );
        $this->setLocalization(
            'eng',
            'global',
            'rma',
            'Goods return'
        );
        $this->setLocalization(
            'eng',
            'global',
            'search',
            'Find'
        );
        $this->setLocalization(
            'eng',
            'global',
            'searchText',
            'Enter search term'
        );
        $this->setLocalization(
            'eng',
            'global',
            'selectCurrency',
            'Please select a currency.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'selectLang',
            'Please select a language.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'selectManufacturer',
            'Please select a manufacturer.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'selectStyle',
            'Colour scheme'
        );
        $this->setLocalization(
            'eng',
            'global',
            'selectUpdateFile',
            'Please select a file.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'shippingTime',
            'Calculated delivery time'
        );
        $this->setLocalization(
            'eng',
            'global',
            'shippingTimeLP',
            'Delivery time'
        );
        $this->setLocalization(
            'eng',
            'global',
            'showAllBestsellers',
            'Show all bestsellers'
        );
        $this->setLocalization(
            'eng',
            'global',
            'showAllProductsTaggedWith',
            'Show all tagged items with'
        );
        $this->setLocalization(
            'eng',
            'global',
            'showAllUpcomingProducts',
            'Show all items available soon'
        );
        $this->setLocalization(
            'eng',
            'global',
            'showNone',
            'Hide all'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sitemapGlobalAttributes',
            'Global characteristics'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sitemapKats',
            'All categories'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sitemapNewsCats',
            'News categories'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sitemapSites',
            'All pages'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortAvailability',
            'Stock level'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortDateofissue',
            'Release date'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortEan',
            'GTIN'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortNameAsc',
            'Item name from A to Z'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortNameDesc',
            'Item name from Z to A'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortNewestFirst',
            'Newest first'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortPriceAsc',
            'Price in ascending order'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortPriceDesc',
            'Price in descending order'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortProductno',
            'SKU'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sortWeight',
            'Weight'
        );
        $this->setLocalization(
            'eng',
            'global',
            'specificProducts',
            'Special items'
        );
        $this->setLocalization(
            'eng',
            'global',
            'subtotal',
            'Total'
        );
        $this->setLocalization(
            'eng',
            'global',
            'sumArticles',
            'Items'
        );
        $this->setLocalization(
            'eng',
            'global',
            'supplierStockNotice',
            'Must be ordered. Ready for shipment in %s days after order.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'switchToMobileTemplate',
            'Would you like to go to the mobile-friendly version of this website?'
        );
        $this->setLocalization(
            'eng',
            'global',
            'topReviews',
            'Top rated'
        );
        $this->setLocalization(
            'eng',
            'global',
            'topsearch',
            'Top search queries '
        );
        $this->setLocalization(
            'eng',
            'global',
            'toptags',
            'Most frequently assigned tags '
        );
        $this->setLocalization(
            'eng',
            'global',
            'trustedshopsRating',
            'Customer review'
        );
        $this->setLocalization(
            'eng',
            'global',
            'trustedShopsRecommended',
            'recommended'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ts_comment',
            'As an additional service we offer you the Trusted Shops buyer protection. '
            . 'You do not have to pay anything for this service; you only have to register.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ts_info_classic_title',
            'More information on'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ts_register',
            'Register for Trusted Shops buyer protection'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ts_seal_classic_title',
            'Click on the Trustmark to check its validity.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ts_signtitle',
            'Trusted Shops Trustmark – Check validity here!'
        );
        $this->setLocalization(
            'eng',
            'global',
            'upcomingProducts',
            'Available soon'
        );
        $this->setLocalization(
            'eng',
            'global',
            'uploadAdded',
            'Uploaded on'
        );
        $this->setLocalization(
            'eng',
            'global',
            'uploadCanceled',
            'Data upload has been cancelled.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'uploadEmptyFile',
            'The selected file is empty.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'uploadError',
            'Could not carry out upload. Please try again.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'uploadInvalidFormat',
            'The file does not match the required format.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'uploadState',
            'Status'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ustIDCaseFive',
            'The VAT ID is invalid according to the VIES system.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ustIDCaseTwo',
            'The VAT ID format is invalid.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ustIDCaseTwoB',
            'For your country the VAT ID should have the following format:'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ustIDError100',
            'The VAT ID country code must start with two capital letters.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ustIDError110',
            'The VAT ID has an invalid length.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'ustIDError120',
            'The VAT ID does not comply with the regulations of your country!<br>The error occurred here: '
        );
        $this->setLocalization(
            'eng',
            'global',
            'ustIDError130',
            'There is no country with the following code: '
        );
        $this->setLocalization(
            'eng',
            'global',
            'ustIDError200',
            'The VIES service for your country is not available until: '
        );
        $this->setLocalization(
            'eng',
            'global',
            'validUntil',
            'Valid until'
        );
        $this->setLocalization(
            'eng',
            'global',
            'Votes',
            'Reviews'
        );
        $this->setLocalization(
            'eng',
            'global',
            'voucher',
            'Coupon'
        );
        $this->setLocalization(
            'eng',
            'global',
            'weightUnit',
            'kg'
        );
        $this->setLocalization(
            'eng',
            'global',
            'wishlist',
            'Wish list'
        );
        $this->setLocalization(
            'eng',
            'global',
            'wrb',
            'Withdrawal'
        );
        $this->setLocalization(
            'eng',
            'global',
            'wrbform',
            'Model instructions on withdrawal'
        );
        $this->setLocalization(
            'eng',
            'global',
            'youAreLoggedIn',
            'You are logged on.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'yourSearch',
            'Your search term was:'
        );
        $this->setLocalization(
            'eng',
            'basketpreview',
            'yourBasket',
            'Recently added to basket'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'addAllToCompareList',
            'Compare all items'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'addToCompare',
            'Add to comparison list'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'addToWishlist',
            'Add to wish list'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'differentialPriceFrom',
            'Price from'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'differentialPriceTo',
            'Price until'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'no_products_in_category',
            'There are no items in this category. Please select a subcategory.'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'noResults',
            'Unfortunately, there are no results for this search term. Please enter a different search term.'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'previous',
            'Back'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'pricePerUnit',
            'Unit price'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'productsSearchTerm',
            'Frequently searched for'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'productsTaggedAs',
            'Frequently tagged'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'purchaseIntervall',
            'Permissible order quantity'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'ribbon-1',
            'Best sellers'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'ribbon-2',
            'Sale %s'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'ribbon-3',
            'New'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'ribbon-4',
            'Top'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'ribbon-5',
            'Available soon'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'ribbon-6',
            'Top rated'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'ribbon-7',
            'Out of stock'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'ribbon-8',
            'In stock'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'ribbon-9',
            'Pre-order'
        );
        $this->setLocalization(
            'eng',
            'productOverview',
            'sorting',
            'Sort order'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'addTag',
            'Your item tag'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'addToCompare',
            'Add to comparison list'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'addToWishlist',
            'Add to wish list'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'addYourTag',
            'Add your item tag'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'articledetails',
            'Item details'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'basketMatrix',
            'Please enter the requested quantity.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'buyProductBundle',
            'Buy this product now in a set!'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'clicktozoom',
            'Click to enlarge'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'configChooseMaxComponents',
            'Please select a maximum of %d.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'configChooseMinComponents',
            'Please select at least %d.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'configChooseNComponents',
            'Please select %d.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'configChooseOneComponent',
            'Please select only one component.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'configComponent',
            'Component'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'configComponents',
            'Components'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'configError',
            'An error occurred during the configuration. Please try again or contact the Support team, if required.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'contents',
            'Content'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'customerWhoBoughtXBoughtAlsoY',
            'Others also bought:'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'dimension_height',
            'Height'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'dimension_length',
            'Length'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'dimension_width',
            'Width'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'dimensions2d',
            'Dimensions (LxH)'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'excl',
            'excl.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'finalprice',
            'Final price*'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'incl',
            'incl.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'integralQuantities',
            'Split quantity allowed for this item (e.g. 0.5).'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'maximalPurchase',
            'You can purchase a maximum of %s %d of this item.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'mbm',
            'Min. purchase quantity'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'minimumPurchase',
            'Please note the minimum purchase quantity of %d %s.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'nextProduct',
            'Go to next item'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'otherProductsFromManufacturer',
            'Other items by'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'outofstock',
            'sold out'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'pleaseChooseVariation',
            'Please select a variation.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'plus',
            'plus'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'previousProduct',
            'Go to previous item'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'priceAsConfigured',
            'Price as configured'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'priceFlow',
            'Price history'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'priceFlowProduct',
            'Price history of'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'priceForAll',
            'Total price'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'pricePerUnit',
            'Unit price'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'productInStock',
            'Item in stock'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'productOutOfStock',
            'Item currently out of stock'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'productQuestion',
            'Question about item'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'productTags',
            'Item tags'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'productTagsDesc',
            'Other customers marked this item with these tags. Item tags are keywords under which other '
            . 'customers can find the items more easily.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'productUnsaleable',
            'This item is currently not available. We are not sure whether and when this item will be available again.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'productVote',
            'Review for this item'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'purchaseIntervall',
            'Permissible order quantity'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'question',
            'Your question'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'recommendLogin',
            'Please log on.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'RelatedProducts',
            'Similar items'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'selectionNotAvailable',
            'The selected combination is not available.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'selectVarCombi',
            'Please select %s.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'sendComment',
            'Submit review'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'sendQuestion',
            'Submit question'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'showOriginalPic',
            'View original image'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'suggestedPrice',
            'Manufacturers RRP'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'suggestedPriceExpl',
            '** Recommended retail price'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'taglogin',
            'Log in'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'tagloginnow',
            'Please log in to add tags to the item. Item tags are keywords under which other customers '
            . 'can find the items more easily.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'takeHeedOfInterval',
            'Please note the permissible order quantity of %d %s.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'thatIs',
            'i.e.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'thisProductIsFrom',
            'This item is available from'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'units',
            'Units'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'updatingStockInformation',
            'Loading stock level information for variations…'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'vat',
            'VAT'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'VotesUser',
            'Customer reviews'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'youSave',
            'Save'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'accountNo',
            'Account number'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'additionalCharges',
            'If goods are delivered to non-EU countries, additional custom duties, taxes or fees may have '
            . 'to be paid by the customer, though not to the supplier, but to the responsible customs or tax '
            . 'authorities. We recommend asking the customs or tax authorities for details before ordering.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'backToBasket',
            'Back to basket'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'banktransferDesc',
            'We will process your order immediately.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'bic',
            'BIC/SWIFT'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'cancellationPolicyNotice',
            'I have read and agree with the <a href="%s" %s>Withdrawal</a>.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'cashOnDeliveryDesc',
            'We will ship your order immediately.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'cashOnDeliveryFee',
            'Please note the additional cash on delivery charge of EUR 2.00 which will be charged by the postman.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'checkOrderDetails',
            'Please check the data. You can correct individual details by clicking on the respective order step.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'checkPLZCity',
            'Please check your postal code and city.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'currentCoupon',
            'Already claimed coupon: '
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'digitalProductsRegisterInfo',
            'Only registered customers can order download items. Please create a customer account or log '
            . 'in with your access data to continue with the purchase.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'discountForArticle',
            'Valid for: '
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'doFollowingBanktransfer',
            'Please make the following cash transfer:'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'emptybasket',
            'There are no items in the basket.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'estimateShipping',
            'Determine shipping costs'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'estimateShippingCostsTo',
            'Determine shipping costs according to'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'fillPayment',
            'Please select a payment method.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'fillShipping',
            'Please select a shipping method.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'fillUnregForm',
            'Please complete the form.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'forInternationalBanktransfers',
            'For foreign bank transfers'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'goOnShopping',
            'View more items'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'goToStartpage',
            'Go to start page'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'guestOrRegistered',
            'You can place an order as a visitor or create a new customer account.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'invalidCouponCode',
            'The entered coupon code is invalid.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'invalidUserdata',
            'The entered user name and/or password are wrong!'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'invoiceDesc',
            'We will process your order immediately.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'login',
            'Log in'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'loginForRegisteredCustomersDesc',
            'You have already ordered from us and have a customer account? Enter your data here.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'merchandiseValue',
            'Goods value'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'minordernotreached',
            'You have not reached the minimum order value. The minimum order value is'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'missingFilesUpload',
            'Please upload all necessary files.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'missingProducts',
            'Some items in your basket are out of stock. Your basket has been updated.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'modifyBasket',
            'Edit basket'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'modifyPaymentOption',
            'Edit payment method'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'modifyShippingAdress',
            'Edit shipping address'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'modifyShippingAdressSimple',
            'Edit'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'modifyShippingOption',
            'Edit shipping method'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'nextStepCheckout',
            'Proceed to checkout'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'noShippingAvailable',
            'There is no shipping method available for this destination. '
            . 'Please contact us directly to complete this order.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'noShippingMethodsAvailable',
            'There is no shipping method available for your order. Please contact us directly to complete this order.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'one-off',
            'Included only once'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'orderCompletedPost',
            'We have received your order.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'orderConfirmationPost',
            'Your order has been completed successfully.<br> '
            . 'You will receive a confirmation email with the respective order data in a few minutes.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'orderLiableToPay',
            'Buy now'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'orderNotPossibleNow',
            'You have recently placed an order. Further orders are possible after 2 minutes. '
            . 'If you try to place another order before the time is up, the waiting time is doubled, '
            . 'i.e. to 4, 8, 16, 32, etc. minutes.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'orderStep0Title2',
            'Select the way you want to order.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'orderWithoutRegistrationDesc',
            'Order quickly and easily without creating a customer account.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'paymentNotNecessary',
            'No payment necessary'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'paypalBtn',
            'Pay now with PayPal'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'paypalDesc',
            'By clicking on this button you will be forwarded to PayPal and can pay your order immediately.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'priceHasChanged',
            'The price of the item "%s" in your basket has changed in the meantime. '
            . 'Please check the line items in the basket.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'pricePerUnit',
            'Unit price'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'proceedNewCustomer',
            'Continue as a new customer'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'product',
            'Item'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'productShippingDesc',
            'Item-specific shipping costs'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'purpose',
            'Payment reference'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'refreshBasket',
            'Please update your basket if you modify the quantities.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'secureCheckout',
            'Secure checkout'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'shipmentMode',
            'Shipping method'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'shippingFor',
            'Shipment of'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'shippingTo',
            'Destination country'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'termsAndConditionsNotice',
            'I have read the <a href="%s" %s>Terms and conditions</a> and agree to them by completing this order.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'totalToPay',
            'Total amount'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'useCoupon',
            'Claim coupon'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'useCredits',
            'Use credit'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'wrb',
            'Withdrawal'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'wrongBIC',
            'The format of the entered BIC is invalid.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'wrongIban',
            'The format of the entered IBAN is invalid.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'yourbasketisempty',
            'Your basket is empty.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'yourbasketismutating',
            'Your basket has been updated due to price or stock level changes. '
            . 'Please check the line items in the basket.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'yourChosenPaymentOption',
            'Your selected payment method'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'yourordercontains',
            'Your current order contains'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'yourOrderId',
            'Your order ID'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'billingAndDeliveryAddress',
            'Billing and shipping address'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'birthday',
            'Date of birth'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'birthdayFormat',
            'DD.MM.YYYY'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'changeBillingAddress',
            'Edit billing address'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'cityNotNumeric',
            'The city must not contain any numbers.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'compareListItemCount',
            'There are %d items on your comparison list.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'contactInformation',
            'Contact data'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'continueOrder',
            'Continue'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'coupon',
            'Redeem coupon'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'couponCode',
            'Coupon code'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'couponDesc',
            'If you have a coupon and would like to redeem it, please enter the coupon code here.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'createNewShippingAdress',
            'Create new shipping address'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'credit',
            'Redeem credit'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'creditDesc',
            'There is still bonus credit left on your customer account.'
            . ' If you would like to use it for this order, please click on the following button. '
        );
        $this->setLocalization(
            'eng',
            'account data',
            'customerOpenOrders',
            'There are %d open orders%s in your name. If you delete your customer account now, '
            . 'your data will be deleted as soon as all orders are complete.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'customerOrdersInCancellationTime',
            ' and %d orders whose return period is not over yet.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'deviatingDeliveryAddress',
            'Different shipping address'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'editBillingAdress',
            'Edit billing address'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'editCustomerData',
            'Edit customer data'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'emailAlreadyExists',
            'There is already a customer account with this email address registered at our online shop. '
            . 'If you want to complete the order with your existing customer account, please log in '
            . 'with your email address and password. If you want to continue as a visitor, '
            . 'deactivate the option "Create new customer account".'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'emailNotAvailable',
            'This email address is already in use.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'firmext',
            'Company 2'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'firstNameNotNumeric',
            'The first name must not contain any numbers.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'formToFast',
            'Sorry, you were a bit too fast.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'invalidCustomer',
            'Invalid customer. The customer is locked.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'invalidHash',
            'Invalid hash passed. Your link may have expired. Please try again to reset your password.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'lastNameNotNumeric',
            'The last name must not contain numbers.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'newsletterInfo',
            ', if you would like to be informed about current offers in our online shop.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'newsletterSubscribe',
            'Subscribe to newsletter (you can unsubscribe at any time)'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'noDnsEmail',
            'Your email address could not be assigned a DNS entry.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'noOrdersYet',
            'You have not created any orders yet.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'noWishlist',
            'No wish list found.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'passwordRepeat',
            'Password (re-enter)'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'passwordsMustBeEqual',
            'The entered passwords must match.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'plz',
            'Postal code'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'privacyAccepted',
            'Data protection regulations accepted'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'read',
            'Read'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'salutation',
            'Title'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'senderEmail',
            'Sender email address'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'shippingAdressDesc',
            'If the billing address is different from the shipping address, please enter the billing address here.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'shippingAdressEqualBillingAdress',
            'Identical shipping and billing address'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'shippingAndPaymentOptions',
            'Shipping and payment method'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'street',
            'Street name'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'street2',
            'Address 2'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'streetnumber',
            'Street number'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'title',
            'Academic title'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'useCredit',
            'Credit used'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'ustid',
            'VAT ID'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'wishlists',
            'Wish list'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'www',
            'WWW'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'yourShippingAdressDesc',
            'Enter the shipping address here if it differs from the bill to address.'
        );
        $this->setLocalization(
            'eng',
            'shipping payment',
            'accountNo',
            'Account number'
        );
        $this->setLocalization(
            'eng',
            'shipping payment',
            'bankname',
            'Bank'
        );
        $this->setLocalization(
            'eng',
            'shipping payment',
            'confirmDataDesc',
            'If your data is correct, you can complete your order.<br>'
            . ' You will receive a confirmation of your order by email.'
        );
        $this->setLocalization(
            'eng',
            'shipping payment',
            'creditcardNo',
            'Credit card number'
        );
        $this->setLocalization(
            'eng',
            'shipping payment',
            'owner',
            'Holder'
        );
        $this->setLocalization(
            'eng',
            'shipping payment',
            'paymentOptionsDesc',
            'Please select a payment method:'
        );
        $this->setLocalization(
            'eng',
            'shipping payment',
            'shippingOptionsDesc',
            'Please select a shipping method:'
        );
        $this->setLocalization(
            'eng',
            'login',
            'basket2PersMerge',
            'Would you like to combine the saved basket with your current basket?'
        );
        $this->setLocalization(
            'eng',
            'login',
            'changePasswordDesc',
            'Please fill in the form in order to change your password.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'changepasswordFilloutForm',
            'Please fill in the form in order to change your password.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'changepasswordPassesNotEqual',
            'Passwords do not match. Please fill in the form again.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'changepasswordPassTooShort',
            'Your new password is too short. Please enter a longer password.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'changepasswordSuccess',
            'You have successfully changed your password.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'changepasswordWrongPass',
            'The old password is not correct. Please enter it again.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'dataEditSuccessful',
            'Data updated successfully.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'deleteAccount',
            'Delete customer account'
        );
        $this->setLocalization(
            'eng',
            'login',
            'editBillingAdressDesc',
            'Update your data here.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'editData',
            'Edit invoice data'
        );
        $this->setLocalization(
            'eng',
            'login',
            'kwkEmail',
            'Email address'
        );
        $this->setLocalization(
            'eng',
            'login',
            'kwkName',
            'Tell a friend!'
        );
        $this->setLocalization(
            'eng',
            'login',
            'loginDesc',
            'You are not logged in. Please enter your data to log in.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'moneyOnAccount',
            'Credit'
        );
        $this->setLocalization(
            'eng',
            'login',
            'newAccount',
            'Create new customer account'
        );
        $this->setLocalization(
            'eng',
            'login',
            'newPasswordRpt',
            'New password (re-enter)'
        );
        $this->setLocalization(
            'eng',
            'login',
            'notPayedYet',
            'Not paid yet'
        );
        $this->setLocalization(
            'eng',
            'login',
            'orderDetails',
            'Order details for'
        );
        $this->setLocalization(
            'eng',
            'login',
            'orderNo',
            'Order ID'
        );
        $this->setLocalization(
            'eng',
            'login',
            'orderStatus',
            'Status'
        );
        $this->setLocalization(
            'eng',
            'login',
            'passwordhasUsername',
            'The password must not contain the users name.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'passwordIsMedium',
            'Mediocre; try to use special characters.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'passwordIsWeak',
            'Weak; try a combination of letters and numbers.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'passwordTooShort',
            'The password must consist of at least %s characters.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'payedOn',
            'Paid on'
        );
        $this->setLocalization(
            'eng',
            'login',
            'reallyDeleteAccount',
            'Do you really want to delete your customer account?'
        );
        $this->setLocalization(
            'eng',
            'login',
            'shippingInfo',
            'Additional shipping information'
        );
        $this->setLocalization(
            'eng',
            'login',
            'typeYourPassword',
            'Please enter a password.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistAddAllToCart',
            'Add all to basket'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistAddNew',
            'Create new wish list'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistaddToCart',
            'Add to basket'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlisteDelete',
            'Delete'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistEmailCount',
            'Maximum number of email recipients'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistEmails',
            'Email recipient – separated by blank spaces'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistNoticePrivate',
            'At the moment your wish list is not public.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistNoticePublic',
            'At the moment your wish list is public.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistNotPrivat',
            'Set wish list to "Public"'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistPosCount',
            'Quantity'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistPrivat',
            'Set wish list to "Private"'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistProduct',
            'Item'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistremoveItem',
            'Remove item'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistRemoveSearch',
            'Clear search'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistRename',
            'Rename wish list'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistSearch',
            'Search for items on your wish list'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistSearchBTN',
            'Find'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistSearchInfo',
            'Find items on your wish list.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistSend',
            'Send wish list'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistSetPrivate',
            'Set to "Private"'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistSetPublic',
            'Set to "Public"'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistUpdate',
            'Update wish list'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistURL',
            'Link to your wish list'
        );
        $this->setLocalization(
            'eng',
            'login',
            'wishlistViaEmail',
            'Send wish list by email'
        );
        $this->setLocalization(
            'eng',
            'login',
            'yourMoneyOnAccount',
            'Your current credit'
        );
        $this->setLocalization(
            'eng',
            'login',
            'yourOrderComment',
            'Your comment on the order'
        );
        $this->setLocalization(
            'eng',
            'login',
            'yourWishlist',
            'Your saved wish lists'
        );
        $this->setLocalization(
            'eng',
            'forgot password',
            'createNewPassword',
            'Request new password'
        );
        $this->setLocalization(
            'eng',
            'forgot password',
            'forgotPasswordDesc',
            'Please enter the email address you registered with.'
        );
        $this->setLocalization(
            'eng',
            'forgot password',
            'newPasswortWasGenerated',
            'In a few minutes you will receive an email with further steps to reset your password.'
        );
        $this->setLocalization(
            'eng',
            'contact',
            'messageSent',
            'Your message has been sent. Thank you for your message.'
        );
        $this->setLocalization(
            'eng',
            'contact',
            'noSubjectAvailable',
            'No subject'
        );
        $this->setLocalization(
            'eng',
            'contact',
            'youSentUsAMessageShortTimeBefore',
            'You have recently sent us a message. Please wait a moment before sending us another message.'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'addtocart',
            'Add to basket'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'addtowishlist',
            'Add to wish list'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'back',
            'Return to online shop'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'compareList',
            'Compare items'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'comparePrintThisPage',
            'Print comparison list'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'goToCompareList',
            'Go to comparison list'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'productNumber',
            'SKU'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'productNumberHint',
            'Please add at least two items to the comparison list.'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'productWeight',
            'Item weight'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'removeFromCompareList',
            'Remove item from comparison list'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'shortDescription',
            'Brief description'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'weightUnit',
            'kg'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'allreadyWroteReview',
            'You have already reviewed this item.'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'averageProductRating',
            'Average item rating'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'balance bonus',
            'Credit bonus'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'edit',
            'Edit review'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'evaluatedWith',
            'rated with'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'feedback activated',
            'The review has been approved.'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'feedback deactivated',
            'The review has not been approved yet.'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'fiveStarsFTW',
            '5 Stars = best rating'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'goButton',
            'OK'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'highestReviewFirst',
            'Best-rated first'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'isRatingHelpful',
            'Is this review helpful to you?'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'latestReviewFirst',
            'Oldest first'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'loginFirst',
            'Please login first to write a review.'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'lowestReviewFirst',
            'Low-rated first'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'maxProduct1',
            'This item has'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'maxProduct2',
            'reviews'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'maxProductNull',
            'no'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'moreProductRatings',
            'More item reviews '
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'no feedback',
            'No item ratings'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'productAssess',
            'Write a review'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'productNotBuyed',
            'You can only write a review for items you bought.'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'productRating',
            'Item review'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'ratingHelpfulCount',
            'people found this helpful.'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'ratingHelpfulCountExt',
            'person found this helpful.'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'recentReviewFirst',
            'Newest first'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'reply',
            'Reply by'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'reviewsInCurrLang',
            'Reviews in the current language:'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'reviewsSortedBy',
            'Sort reviews by'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'shareYourExperience',
            'Share your experiences with other customers!'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'shareYourRatingGuidelines',
            'Tell us what you think!'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'submitRating',
            'Submit review'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'theMostUsefulRating',
            'Most helpful item review'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'unusefulClassifiedReviewFirst',
            'Least helpful first'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'usefulClassifiedReviewFirst',
            'Most helpful first'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'viewMoreRatingsInOtherLanguages',
            'More reviews in other languages'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'wroteAt',
            'wrote on'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newsletterdraftdate',
            'Sent on'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newsletteremail',
            'Email'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newsletterHtml',
            'Content (HTML)'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newsletterSubscribe',
            'Subscribe to newsletter (you can unsubscribe at any time)'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newsletterSubscribeDesc',
            'To subscribe to our newsletter, please enter your email address here and then click on "Subscribe".'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newslettertitle1',
            'Ms'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newslettertitle2',
            'Mr'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newsletterUnsubscribe',
            'Unsubscribe newsletter'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newsletterUnsubscribeDesc',
            'To unsubscribe from the newsletter, please enter your email address into the field below.'
            . ' Then click on "Unsubscribe".'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'unsubscribeAnytime',
            'Subscribe to the newsletter now and never miss the latest offers again! You can unsubscribe at any time.'
        );
        $this->setLocalization(
            'eng',
            'news',
            'monthOverview',
            'News: months with news postings'
        );
        $this->setLocalization(
            'eng',
            'news',
            'moreLink',
            'Continue'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsCat',
            'Category of the news system'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsCatOverview',
            'Overview of the categories of the news system'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsCats',
            'Categories of the news system'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsCommentSave',
            'Save'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsEmail',
            'Email'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsLogin',
            'Please sign in to write a comment.'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsLoginNow',
            'Log in now'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsMetaDesc',
            'News and current information about our product range and our online shop'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsMonthOverview',
            'News: months with news postings'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsPerSite',
            'Number per page'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsRestricted',
            'This posting is subject to restrictions.'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsSort',
            'Sort order'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsSortCommentsASC',
            'Comment number ascending'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsSortCommentsDESC',
            'Comment number descending'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsSortDateDESC',
            'Most recent first'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsSortHeadlineASC',
            'Headline A–Z'
        );
        $this->setLocalization(
            'eng',
            'news',
            'newsSortHeadlineDESC',
            'Headline Z-A'
        );
        $this->setLocalization(
            'eng',
            'news',
            'noNewsArchiv',
            'Unfortunately, there are no news postings in the archive.'
        );
        $this->setLocalization(
            'eng',
            'umfrage',
            'umfrage',
            'Survey'
        );
        $this->setLocalization(
            'eng',
            'umfrage',
            'umfrageNext',
            'Next'
        );
        $this->setLocalization(
            'eng',
            'umfrage',
            'umfrageQRequired',
            '(*) = Required information'
        );
        $this->setLocalization(
            'eng',
            'umfrage',
            'umfrageSubmit',
            'Submit survey'
        );
        $this->setLocalization(
            'eng',
            'strength',
            'medium',
            'mediocre'
        );
        $this->setLocalization(
            'eng',
            'strength',
            'strong',
            'strong'
        );
        $this->setLocalization(
            'eng',
            'strength',
            'stronger',
            'very strong'
        );
        $this->setLocalization(
            'eng',
            'strength',
            'veryWeak',
            'too short'
        );
        $this->setLocalization(
            'eng',
            'strength',
            'weak',
            'weak'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'bcOrder',
            'Order'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'bcWishlist',
            'Wish list'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'bewertung',
            'Review'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'checkout',
            'Order process'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'newskat',
            'Category of the news system'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'register',
            'Register'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'startpage',
            'Homepage'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'umfrage',
            'Survey'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'umfragen',
            'Surveys'
        );
        $this->setLocalization(
            'eng',
            'breadcrumb',
            'wishlist',
            'Wish list'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCosts',
            'Free shipping'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCostsAt',
            'Another %s and your order will be eligible for free shipping with %s %s'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCostsAtExtended',
            'to %s.'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCostsReached',
            'Your order can be shipped for free with %s %s.'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'orderExpandInventory',
            'The following items are not available in the selected quantity. Therefore delivery may be delayed: %s'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'shipping',
            'shipping costs'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'shippingInformation',
            'plus <a href="%1$s" class="shipment popup">shipping costs</a>'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'shippingInformationSpecific',
            'plus <a href="%1$s" class="shipment popup">shipping costs</a> starting from %2$s for delivery to %3$s'
        );
        $this->setLocalization(
            'eng',
            'order',
            'packageTracking',
            'Tracking'
        );
        $this->setLocalization(
            'eng',
            'order',
            'partialShipped',
            'Partial delivery'
        );
        $this->setLocalization(
            'eng',
            'order',
            'partialShippedCount',
            'Number'
        );
        $this->setLocalization(
            'eng',
            'order',
            'partialShippedDate',
            'Shipped on'
        );
        $this->setLocalization(
            'eng',
            'order',
            'partialShippedPosition',
            'Line item'
        );
        $this->setLocalization(
            'eng',
            'order',
            'shippingOrder',
            'Delivery note'
        );
        $this->setLocalization(
            'eng',
            'order',
            'statusCancelled',
            'Cancelled'
        );
        $this->setLocalization(
            'eng',
            'order',
            'statusPartialShipped',
            'Partially delivered'
        );
        $this->setLocalization(
            'eng',
            'order',
            'statusPending',
            'Open'
        );
        $this->setLocalization(
            'eng',
            'order',
            'statusProcessing',
            'Being processed'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'accountDeleted',
            'Y ur customer account was deleted successfully.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'artikelVariBoxEmpty',
            'Please enter at least the item quantity into the variation box.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'availAgainOptinCreated',
            'Thank you, we have received your data. We have sent you an email with an activation code. '
            . 'Please click on the link in this email if you would like to be notified when'
            . ' this item is available again.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'basketAdded',
            'This item has been added to your basket.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'basketAjaxAdded',
            'Item %s has been added to your basket. (%dx)'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'basketAllAdded',
            'The selected items have been added to the basket.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'bewertungBewadd',
            'Thank you, your review has been submitted.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'bewertungBewaddacitvate',
            'Your review has been submitted and must now be approved by the seller.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'bewertungBewaddCredits',
            'Your review has been submitted and the amount of %s has been added to your customer account.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'bewertungHilfadd',
            'Your opinion on this review has been submitted.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'bewertungHilfchange',
            'Your opinion on this review has been edited.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'chooseVariations',
            'This item has variations. Please select the requested variation.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'comparelistProductadded',
            'The selected item has been added to the comparison list.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'comparelistProductexists',
            'The selected item is already on the comparison list.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'continueAfterActivation',
            'You can continue with the ordering process as soon as your customer account has been activated.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'emailSeccessfullySend',
            'Your wish list has been sent successfully.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'kwkAdd',
            'Your invitation has been successfully sent to %s.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'loginWishlist',
            'Please log in to add items to your wish list.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'maxTagsExceeded',
            'We are sorry. You have already added all the item tags you are allowed to add in 24 hours.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'newscommentAdd',
            'Your comment has been saved successfully.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'newscommentAddactivate',
            'Your comment has been saved successfully and must now be approved by the seller.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'newsletterActive',
            'Your email address has been successfully activated for our newsletter.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'newsletterAdd',
            'Thank you, we have received your data. We have sent you an email with an activation code. '
            . 'Please click on the link in the email to complete your subscription to the newsletter.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'newsletterNomailAdd',
            'Thank you, you have successfully subscribed to the newsletter.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'noEmail',
            'Please enter at least a valid email address.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'noProductWishlist',
            'The following items on your wish list are no longer available in our range of products: '
        );
        $this->setLocalization(
            'eng',
            'messages',
            'notificationNotPossible',
            'You have recently sent a notification request. Please wait a moment '
            . 'before submitting a new notification request.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'novalidEmail',
            'No email could be sent to the following email addresses: '
        );
        $this->setLocalization(
            'eng',
            'messages',
            'nowlidWishlist',
            'The wish list with ID "%s" is no longer public or has been deleted.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'optinCanceled',
            'You have successfully cancelled your permission.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'optinRemoved',
            'Your activation request has been deleted.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'optinSucceeded',
            'The activation was successful.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'optinSucceededAgain',
            'The activation has already been completed.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'optinSucceededMailSent',
            'You should already have received an email containing your activation code.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'pleaseLogin',
            'Please sign in to shop in our online shop.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'pleaseLoginToAddTags',
            'Please sign in to add item tags.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'pollAdd',
            'Thank you for participating in our survey.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'pollCoupon',
            'Thank you for participating in our survey. For your next order the following coupon code is available: %s.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'pollCredit',
            'Thank you for participating in our survey. You have received a credit of %s.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'pollError',
            'An error occurred during evaluation. Please try again.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'pollExtrapoint',
            'Thank you for participating in our survey. %s bonus points have been added to your account.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'preorderNotPossible',
            'It is not possible to pre-order this item.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'quantityNotAvailable',
            'The requested item quantity is not available. Please enter a smaller quantity.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'quantityNotAvailableVar',
            'The requested item quantity is not available for this variation. '
            . 'Please enter a smaller quantity or select a different variation.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'questionNotPossible',
            'You have recently sent a question about the item. Please wait a moment before'
            . ' submitting a new question about the item.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'recommentQuestionNotPossible',
            'You have recently sent an item recommendation. Please wait a moment'
            . ' before submitting a new recommendation.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'tagAccepted',
            'The item tag has been added.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'tagAcceptedWaitCheck',
            'The item tag has been added and must now be approved by the administrator.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'tagArtikelEmpty',
            'Please enter a name for the item tag.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'thankYouForComment',
            'Thank you for your review.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'thankYouForNotificationSubscription',
            'Thank you. We will inform you as soon as the item is available again.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'thankYouForQuestion',
            'Thank you for your question about the item.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'thankYouForRecommend',
            'Thank you for the recommendation.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wishlistAdd',
            'Your wish list has been saved successfully.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wishlistDelAll',
            'All items on your wish list have been deleted.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wishlistDelete',
            'Your wish list has been deleted.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wishlistProductadded',
            'The selected item has been added to your wish list.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wishlistSetPrivate',
            'Your wish list is now private.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wishlistSetPublic',
            'Your wish list is now public.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wishlistStandard',
            'Another wish list has been activated.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wishlistUpdate',
            'Your wish list has been updated.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wkMaxorderlimit',
            'Unfortunately, the order quantity for this item is too high.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wkOnrequest',
            'Item price on request'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wkPurchaseintervall',
            'The order quantity for this item must be a multiple of the permissible order quantity.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'wkUnsalable',
            'This item is not for sale.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'yourQuantity',
            'Your requested quantity is '
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'bewertungBewexist',
            'You have already reviewed this item. '
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'bewertungBewnotbought',
            'You can only write reviews for item you have already bought.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'cartPersRemoved',
            'Item "%s" could not be added to the basket.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'compareMaxlimit',
            'We are sorry; your comparison list has reached the maximum number of items. '
            . 'Please remove items from the comparison list to add new items. '
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'freegiftsMinimum',
            'The minimum order value required for the free gift has not yet been reached.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'freegiftsNogifts',
            'Currently there are no free gifts available.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'freegiftsNostock',
            'The free gift is sold out.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'kwkAlreadyreg',
            'The email address %s is already in use.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'kwkEmailblocked',
            'The email address is locked.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'kwkWrongdata',
            'Please enter a valid date.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'mandatoryFieldNotification',
            'Please fill out all mandatory fields.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'missingParamShippingDetermination',
            'Please fill in the country and postal code correctly.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'missingTaxZoneForDeliveryCountry',
            'Shipments to %s are currently not possible as no valid tax zone has been defined.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newscommentAlreadywritten',
            'You have already reached the maximum number of comments for this news posting.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newscommentLongtext',
            'You have exceeded the allowed maximum of 1000 characters for the comment.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newscommentMissingnameemail',
            'Please enter a name and an email address.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newscommentMissingtext',
            'Please enter a text.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newsletterCaptcha',
            'Please note the Captcha.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newsletterNoactive',
            'The activation code could not be found.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newsletterNocode',
            'The delete code could not be found in the database. '
            . 'Please check your input for potential typing errors and contact the Support team, if required.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newsletterNoexists',
            'Your email address does not exist in the database. '
            . 'Please check your input for potential typing errors and contact the Support team, if required.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newsletterNoname',
            'Please enter a first and a last name.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newsletterWrongemail',
            'We are sorry; your email address has an invalid format.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'noCookieDesc',
            'To use our site you have to activate cookies in your browser.<br />'
            . 'After activation, please try to open our <a href="index.php">homepage</a> again.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'noCookieHeader',
            'Activate cookies'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'noMediaFile',
            'There are no media files available.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'optinActionUnknown',
            'Unknown activity requested. Please contact the Support team.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'optinCodeUnknown',
            'Unknown confirmation code. Please check your input for potential typing'
            . ' errors and contact the Support team, if required.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'pollAlreadydid',
            'We are sorry; you have completed this survey.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'pollNopoll',
            'Currently there are no surveys available.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'pollPleaselogin',
            'You must be logged in to participate in the survey.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'pollRequired',
            'Please answer all mandatory questions.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'productquestionPleaseLogin',
            'We are sorry; you must be logged in to ask a question about an item.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'ratingRange',
            'The rating must be a number from 1 to 5.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'statusOrderNotFound',
            'No matching order found.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'uidNotFound',
            'There is no order with the requested ID.'
        );
        $this->setLocalization(
            'eng',
            'redirect',
            'poll',
            'Survey'
        );
        $this->setLocalization(
            'eng',
            'redirect',
            'recommend',
            'Recommend item'
        );
        $this->setLocalization(
            'eng',
            'redirect',
            'rma',
            'Goods return'
        );
        $this->setLocalization(
            'eng',
            'redirect',
            'tag',
            'Item tag'
        );
        $this->setLocalization(
            'eng',
            'redirect',
            'wishlist',
            'Wish list'
        );
        $this->setLocalization(
            'eng',
            'media',
            'sliderNext',
            'Next'
        );
        $this->setLocalization(
            'eng',
            'media',
            'sliderPrev',
            'Back'
        );
        $this->setLocalization(
            'eng',
            'media',
            'tabMisc',
            'Others'
        );
        $this->setLocalization(
            'eng',
            'media',
            'tabMusic',
            'Music'
        );
        $this->setLocalization(
            'eng',
            'media',
            'tabPicture',
            'Images'
        );
        $this->setLocalization(
            'eng',
            'paymentMethods',
            'errorMailBody',
            'The following error occurred in your online shop %s while initialising'
            . ' the payment process with the payment method %s: %s.'
        );
        $this->setLocalization(
            'eng',
            'paymentMethods',
            'errorMailSubject',
            'Error in your online shop %s'
        );
        $this->setLocalization(
            'eng',
            'paymentMethods',
            'errorText',
            'An error occurred while initialising the payment process. '
            . 'The online shop operator has been notified and will contact you shortly.'
        );
        $this->setLocalization(
            'eng',
            'paymentMethods',
            'paypalError',
            'An error occurred while communicating with PayPal. Your PayPal account may not have been activated yet. '
            . 'If this is not the case, please contact the shop owner and send them the following error code: %s'
        );
        $this->setLocalization(
            'eng',
            'paymentMethods',
            'paypalHttpError',
            'Cannot connect to PayPal server'
        );
        $this->setLocalization(
            'eng',
            'paymentMethods',
            'paypalText',
            'Pay order %s from %s.'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma',
            'Goods return'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_artikelwahl',
            'Go to item selection'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_auswahl',
            'Selection'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_back',
            'Back'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_created',
            'Created'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_error_noarticle',
            'Please select at least one item.'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_error_validquantity',
            'Please make sure that you have specified the item quantity '
            . 'and that it does not exceed the quantity ordered.'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_gekennzeichnet',
            'Already marked as goods return'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_info_success',
            'Goods return with number %s has been saved successfully.'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_login',
            'Please sign in to complete a goods return.'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_month',
            'Months'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_nolicence',
            'No valid licence found for this module.'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_number',
            'Return authorisation number'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_ordertime',
            'Orders of the last'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_print',
            'Print'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_products',
            'Items'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_reason',
            'Return reason'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_required',
            'Mandatory fields'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_ruecksenden',
            'Submit goods return'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_success_msg_1',
            'Thank you for using our goods return.<br /><br />'
            . 'In the following you will find an overview of the items you have selected to be returned.'
        );
        $this->setLocalization(
            'eng',
            'rma',
            'rma_success_msg_2',
            'Please print this overview and include it in the return package.<br /><br />'
            . 'Once we have processed your return, we will notify you by email about the refund (if applicable).'
            . '<br /><br />Thank you for shopping at'
        );
        $this->setLocalization(
            'eng',
            'productDownloads',
            'downloadFileType',
            'File format'
        );
        $this->setLocalization(
            'eng',
            'aria',
            'danger',
            'Context: Caution'
        );
        $this->setLocalization(
            'eng',
            'aria',
            'info',
            'Context: Information'
        );
        $this->setLocalization(
            'eng',
            'aria',
            'primary',
            'Context: primary'
        );
        $this->setLocalization(
            'eng',
            'aria',
            'scrollMenuLeft',
            'scroll to the left'
        );
        $this->setLocalization(
            'eng',
            'aria',
            'scrollMenuRight',
            'scroll to the right'
        );
        $this->setLocalization(
            'eng',
            'aria',
            'secondary',
            'Context: Secondary'
        );
        $this->setLocalization(
            'eng',
            'aria',
            'success',
            'Context: Success'
        );
        $this->setLocalization(
            'eng',
            'aria',
            'warning',
            'Context: Warning'
        );
        $this->setLocalization(
            'eng',
            'aria',
            'wishlistOptions',
            'Wish list menu'
        );
        $this->setLocalization(
            'eng',
            'wishlist',
            'addNew',
            'Create wish list'
        );
        $this->setLocalization(
            'eng',
            'wishlist',
            'setAsStandardWishlist',
            'Add new items to the currently selected list by default'
        );
        $this->setLocalization(
            'eng',
            'wishlist',
            'wlDelete',
            'Delete wish list'
        );
        $this->setLocalization(
            'eng',
            'wishlist',
            'wlRemoveAllProducts',
            'Delete all items'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
