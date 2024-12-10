<?php

/**
 * Add lang var videoTypeNotSupported
 *
 * @author je
 * @created Fr, 29 May 2020 14:18:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200529141800
 */
class Migration20200529141800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'je';
    }

    public function getDescription(): string
    {
        return 'Add lang var videoTypeNotSupported, videoTagNotSupported and audioTagNotSupported';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'errorMessages',
            'videoTypeNotSupported',
            'Dieses Video kann nicht angezeigt werden. Folgende Formate werden unterstützt: .mp4, .ogg und .webm .'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'videoTypeNotSupported',
            'This video cannot be played. Following video types are supported: .mp4, .ogg and .webm .'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'videoTagNotSupported',
            'Das HTML5 <video> Tag wird von Ihrem Browser nicht unterstützt.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'videoTagNotSupported',
            'Your browser does not support the HTML5 <video> tag.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'audioTagNotSupported',
            'Das HTML5 <audio> Tag wird von Ihrem Browser nicht unterstützt.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'audioTagNotSupported',
            'Your browser does not support the HTML5 <audio> tag.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('videoTypeNotSupported');
        $this->removeLocalization('videoTagNotSupported');
        $this->removeLocalization('audioTagNotSupported');
    }
}
