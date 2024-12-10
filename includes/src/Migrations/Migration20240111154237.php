<?php

/**
 * Add new free gift sorting and settings
 *
 * @author tnt
 * @created Thu, 11 Jan 2024 15:42:37 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240111154237
 */
class Migration20240111154237 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'tnt';
    }

    public function getDescription(): string
    {
        return 'Add new free gift sorting and settings';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            configName: 'sonstiges_gratisgeschenk_wk_hinweis_anzeigen',
            configValue: 'N',
            configSectionID: CONF_SONSTIGES,
            externalName: 'Hinweis auf Gratisgeschenke im Warenkorb anzeigen',
            inputType: 'selectbox',
            sort: 651,
            additionalProperties: (object)[
                'cBeschreibung' => 'Aktivieren Sie diese Option, damit Ihre Kunden im Warenkorb sehen, dass Sie ein'
                    . ' Gratisgeschenk wählen können.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );
        $this->setConfig(
            configName: 'sonstiges_gratisgeschenk_noch_nicht_verfuegbar_anzeigen',
            configValue: 'N',
            configSectionID: CONF_SONSTIGES,
            externalName: 'Noch nicht verfügbare Gratisgeschenke anzeigen',
            inputType: 'selectbox',
            sort: 652,
            additionalProperties: (object)[
                'cBeschreibung' => 'Aktivieren Sie diese Option, damit Gratisgeschenke angezeigt werden, auch wenn'
                    . ' deren Mindestbestellwert noch nicht erreicht wurde.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );

        $this->setLocalization(
            'ger',
            'basket',
            'freeGiftsStillMissingAmount',
            'Es fehlen noch %s'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'freeGiftsStillMissingAmount',
            'You are missing %s'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'freeGiftsAvailable',
            'Gratisgeschenk erhältlich'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'freeGiftsAvailable',
            'Free gift available'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'freeGiftsAvailableText',
            'Ihnen steht mindestens ein Gratisgeschenk zur Verfügung.'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'freeGiftsAvailableText',
            'There is at least one free gift available to you.'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'freeGiftsStillMissingAmountForNextFreeGift',
            'Es fehlen noch %s, um aus weiteren Gratisgeschenken wählen zu können.'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'freeGiftsStillMissingAmountForNextFreeGift',
            'If your order value increases by %s, you can choose from a greater selection of free gifts.'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'freeGiftsSeeAll',
            'Alle Gratisgeschenke anzeigen'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'freeGiftsSeeAll',
            'Show all free gifts'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'chooseFreeGiftNow',
            'Jetzt auswählen'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'chooseFreeGiftNow',
            'Select now'
        );
        // @todo Check german and english text with editorial team
        $this->setLocalization(
            'ger',
            'productDetails',
            'productAvailableAsFreeGift',
            'Dieses Produkt steht Ihnen als Gratisgeschenk zur Verfügung. Bitte fügen Sie es direkt in Ihrem'
            . ' Warenkorb hinzu.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'productAvailableAsFreeGift',
            'This product is available to you as a free gift. Please add it directly to your shopping cart.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'freegiftsNotAvailable',
            'Das Gratisgeschenk ist leider nicht verfügbar.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'freegiftsNotAvailable',
            'The free gift is not available.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('sonstiges_gratisgeschenk_noch_nicht_verfuegbar_anzeigen');
        $this->removeConfig('sonstiges_gratisgeschenk_wk_hinweis_anzeigen');
        $this->removeLocalization('basket', 'freeGiftsStillMissingAmount');
        $this->removeLocalization('basket', 'freeGiftsAvailable');
        $this->removeLocalization('basket', 'freeGiftsAvailableText');
        $this->removeLocalization('basket', 'freeGiftsStillMissingAmountForNextFreeGift');
        $this->removeLocalization('basket', 'freeGiftsSeeAll');
        $this->removeLocalization('basket', 'chooseFreeGiftNow');
        $this->removeLocalization('productDetails', 'productAvailableAsFreeGift');
        $this->removeLocalization('errorMessages', 'freegiftsNotAvailable');
    }
}
