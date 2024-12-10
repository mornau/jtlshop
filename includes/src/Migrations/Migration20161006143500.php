<?php

/**
 * removes legal hint from language variable shareYourRatingGuidelines
 *
 * @author ms
 * @created Thu, 06 Oct 2016 14:35:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161006143500
 */
class Migration20161006143500 extends Migration implements IMigration
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
        $this->setLocalization('ger', 'product rating', 'shareYourRatingGuidelines', 'Teilen Sie uns Ihre Meinung mit');
        $this->setLocalization('eng', 'product rating', 'shareYourRatingGuidelines', 'Share your experience');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization(
            'ger',
            'product rating',
            'shareYourRatingGuidelines',
            'Teilen Sie uns Ihre Meinung mit. Bitte beachten Sie dabei unsere Artikelbewertungs-Richtlinien'
        );
        $this->setLocalization(
            'eng',
            'product rating',
            'shareYourRatingGuidelines',
            'Share your experience and please be aware about our post guidelines'
        );
    }
}
