<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230404172100
 */
class Migration20230404172100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Remove index.php url from noCookieDesc alert';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'errorMessages',
            'noCookieDesc',
            'Zur Nutzung unserer Seite müssen Sie im Browser Cookies aktivieren.<br>' .
            'Rufen Sie dann noch einmal unsere <a href="%s">Startseite</a> auf.'
        );

        $this->setLocalization(
            'eng',
            'errorMessages',
            'noCookieDesc',
            'To use our site you have to activate cookies in your browser.<br>' .
            'After activation, please try to open our <a href="%s">homepage</a> again.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
