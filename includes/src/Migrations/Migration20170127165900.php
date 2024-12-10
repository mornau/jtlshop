<?php

/**
 * Add language var "show all reviews" to reset review filter
 *
 * @author dr
 * @created Fri, 27 Jan 2017 16:59:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170127165900
 */
class Migration20170127165900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add language var "show all reviews" to reset review filter';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'product rating', 'allReviews', 'Alle Bewertungen');
        $this->setLocalization('eng', 'product rating', 'allReviews', 'All reviews');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('allReviews');
    }
}
