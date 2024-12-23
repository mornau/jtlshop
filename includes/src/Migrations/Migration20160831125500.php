<?php

/**
 * add_lang_key_footnoteShipping
 *
 * @author ms
 * @created Wed, 31 Aug 2016 12:55:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160831125500
 */
class Migration20160831125500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'footnoteInclusiveShipping', ', inkl. <a href="%s">Versand</a>');
        $this->setLocalization('eng', 'global', 'footnoteInclusiveShipping', ' and <a href="%s">shipping costs</a>');

        $this->setLocalization('ger', 'global', 'footnoteExclusiveShipping', ', zzgl. <a href="%s">Versand</a>');
        $this->setLocalization('eng', 'global', 'footnoteExclusiveShipping', ' plus <a href="%s">shipping costs</a>');

        $this->setLocalization('ger', 'global', 'footnoteInclusiveVat', 'Alle Preise inkl. gesetzlicher USt.');
        $this->setLocalization(
            'eng',
            'global',
            'footnoteInclusiveVat',
            'All prices inclusive legal <abbr title="value added tax">VAT</abbr>'
        );

        $this->setLocalization('ger', 'global', 'footnoteExclusiveVat', 'Alle Preise zzgl. gesetzlicher USt.');
        $this->setLocalization(
            'eng',
            'global',
            'footnoteExclusiveVat',
            'All prices exclusive legal <abbr title="value added tax">VAT</abbr>'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('footnoteInclusiveShipping');
        $this->removeLocalization('footnoteExclusiveShipping');

        $this->setLocalization(
            'ger',
            'global',
            'footnoteInclusiveVat',
            'Alle Preise inkl. gesetzlicher USt., zzgl. <a href="#SHIPPING_LINK#">Versand</a>'
        );
        $this->setLocalization(
            'eng',
            'global',
            'footnoteInclusiveVat',
            'All prices inclusive legal <abbr title="value added tax">VAT</abbr> '
            . 'plus <a href="#SHIPPING_LINK#">shipping costs</a>'
        );

        $this->setLocalization(
            'ger',
            'global',
            'footnoteExclusiveVat',
            'Alle Preise zzgl. gesetzlicher USt., zzgl. <a href="#SHIPPING_LINK#">Versand</a>'
        );
        $this->setLocalization(
            'eng',
            'global',
            'footnoteExclusiveVat',
            'All prices exclusive legal <abbr title="value added tax">VAT</abbr> '
            . 'plus <a href="#SHIPPING_LINK#">shipping costs</a>'
        );
    }
}
