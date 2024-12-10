<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210129122800
 */
class Migration20210129122800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Fix video lang tag';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'errorMessages',
            'videoTagNotSupported',
            'Das HTML5 video-Tag wird von Ihrem Browser nicht unterstützt.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'videoTagNotSupported',
            'Your browser does not support the HTML5 video-tag.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'audioTagNotSupported',
            'Das HTML5 audio-Tag wird von Ihrem Browser nicht unterstützt.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'audioTagNotSupported',
            'Your browser does not support the HTML5 audio-tag.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
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
}
