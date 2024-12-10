<?php

/** fix typo in lang var */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20171214175900
 */
class Migration20171214175900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE tsprachwerte 
                SET cWert = 'Geben Sie die erste Bewertung für diesen Artikel ab "
            . "und helfen Sie Anderen bei der Kaufentscheidung'
                WHERE cName = 'firstReview' 
                AND cWert = 'Geben Sie die erste Bewertung für diesen Artikel ab "
            . "und helfen Sie Anderen bei der Kaufenscheidung'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE tsprachwerte 
                SET cWert = 'Geben Sie die erste Bewertung für diesen Artikel ab "
            . "und helfen Sie Anderen bei der Kaufenscheidung' 
                WHERE cName = 'firstReview' 
                AND cWert = 'Geben Sie die erste Bewertung für diesen Artikel ab "
            . "und helfen Sie Anderen bei der Kaufentscheidung'"
        );
    }
}
