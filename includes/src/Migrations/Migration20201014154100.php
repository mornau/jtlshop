<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201014154100
 */
class Migration20201014154100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add video permit lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'global',
            'allowConsentYouTube',
            'YouTube-Videos zulassen'
        );
        $this->setLocalization(
            'eng',
            'global',
            'allowConsentYouTube',
            'Permit YouTube videos'
        );
        $this->setLocalization(
            'ger',
            'global',
            'allowConsentVimeo',
            'Vimeo-Videos zulassen'
        );
        $this->setLocalization(
            'eng',
            'global',
            'allowConsentVimeo',
            'Permit Vimeo videos'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('allowConsentYouTube');
        $this->removeConfig('allowConsentVimeo');
    }
}
