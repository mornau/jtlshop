<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240905092800
 */
class Migration20240905092800 extends Migration implements IMigration
{
    /**
     * @inheritdoc
     */
    public function getAuthor(): string
    {
        return 'fm';
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return 'Updated privacy policy urls';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE `tconsentlocalization` 
                SET `privacyPolicy` = 'https://policies.google.com/privacy?hl=de'
                WHERE `languageID` = 1 AND `name` = 'YouTube'"
        );
        $this->execute(
            "UPDATE `tconsentlocalization` 
                SET `privacyPolicy` = 'https://policies.google.com/privacy?hl=en'
                WHERE `languageID` = 2 AND `name` = 'YouTube'"
        );
        $this->execute(
            "UPDATE `tconsentlocalization` 
                SET `privacyPolicy` = 'https://vimeo.com/privacy'
                WHERE `languageID` = 1
                  AND `name` = 'Vimeo'
                  AND `privacyPolicy` = 'https://policies.google.com/privacy?hl=de'"
        );
        $this->execute(
            "UPDATE `tconsentlocalization` 
                SET `privacyPolicy` = 'https://vimeo.com/privacy'
                WHERE `languageID` = 2
                  AND `name` = 'Vimeo'
                  AND `privacyPolicy` = 'https://google.com/privacy-policy'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
