<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200923091400
 */
class Migration20200923091400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add missingToken, unknownError messages';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'messages',
            'missingToken',
            'Fehlerhafter Token.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'missingToken',
            'Missing token.'
        );
        $this->setLocalization(
            'ger',
            'messages',
            'unknownError',
            'Ein unbekannter Fehler trat auf.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'unknownError',
            'An unknown error occured.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('missingToken', 'messages');
        $this->removeLocalization('unknownError', 'messages');
    }
}
