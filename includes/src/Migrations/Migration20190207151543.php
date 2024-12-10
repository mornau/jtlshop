<?php

/**
 * add_lang_rating_range_error
 *
 * @author mh
 * @created Thu, 07 Feb 2019 15:15:43 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190207151543
 */
class Migration20190207151543 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang rating range error';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'errorMessages',
            'ratingRange',
            'Die Bewertung muss eine Zahl zwischen 1 und 5 sein.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'ratingRange',
            'The rating needs to be a value between 1 and 5.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('ratingRange');
    }
}
