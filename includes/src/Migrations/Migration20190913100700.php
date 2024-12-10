<?php

/**
 * Change some frontend language variables
 *
 * @author mh
 * @created Fri, 13 Sep 2019 10:07:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Migration
 */
class Migration20190913100700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Change some frontend language variables (NOVA)';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'filterGo', 'Filtern');
        $this->setLocalization('eng', 'global', 'filterGo', 'Filter');
        $this->setLocalization('ger', 'checkout', 'nextStepCheckout', 'Zur Kasse');
        $this->setLocalization('eng', 'checkout', 'nextStepCheckout', 'Checkout');
        $this->setLocalization('ger', 'news', 'moreLink', 'weiterlesen');
        $this->setLocalization('ger', 'news', 'newsCommentSave', 'Kommentar senden');
        $this->setLocalization('eng', 'news', 'newsCommentSave', 'Comment');
        $this->setLocalization('ger', 'wishlist', 'addNew', 'Wunschzettel erstellen');
        $this->setLocalization('eng', 'wishlist', 'addNew', 'Create wishlist');
        $this->setLocalization(
            'ger',
            'news',
            'commentWillBeValidated',
            'Kommentare werden vor der Veröffentlichung geprüft.'
        );
        $this->setLocalization(
            'eng',
            'news',
            'commentWillBeValidated',
            'All comments are reviewed before they are published.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'global', 'filterGo', 'Los');
        $this->setLocalization('eng', 'global', 'filterGo', 'Go');
        $this->setLocalization('ger', 'checkout', 'nextStepCheckout', 'Weiter zur Bestellung');
        $this->setLocalization('eng', 'checkout', 'nextStepCheckout', 'Continue to your order');
        $this->setLocalization('ger', 'news', 'moreLink', 'mehr ...');
        $this->setLocalization('ger', 'news', 'newsCommentSave', 'Speichern');
        $this->setLocalization('eng', 'news', 'newsCommentSave', 'Save');
        $this->setLocalization('ger', 'wishlist', 'addNew', 'Neue erstellen');
        $this->setLocalization('eng', 'wishlist', 'addNew', 'Create new');

        $this->removeLocalization('commentWillBeValidated');
    }
}
