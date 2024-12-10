<?php

/**
 * adds lang var for rating
 *
 * @author ms
 * @created Tue, 12 Jun 2018 12:40:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180612124000
 */
class Migration20180612124000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add lang var for rating';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'product rating', 'reviewsInCurrLang', 'Bewertungen in der aktuellen Sprache:');
        $this->setLocalization('eng', 'product rating', 'reviewsInCurrLang', 'Reviews in current language:');

        $this->setLocalization(
            'ger',
            'product rating',
            'noReviewsInCurrLang',
            'In der aktuellen Sprache gibt es keine Bewertungen.'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'noReviewsInCurrLang',
            'There are no reviews in the current language.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('ratingsInCurrLang');
        $this->removeLocalization('noRatingsInCurrLang');
    }
}
