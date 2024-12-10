<?php

/**
 * ftp settings
 *
 * @author aj
 * @created Mon, 17 Nov 2018 13:33:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181117133301
 */
class Migration20181117133301 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'aj';
    }

    public function getDescription(): string
    {
        return 'FTP settings';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'ftp_header',
            'FTP Verbindung',
            \CONF_FS,
            'FTP Verbindung',
            null,
            100,
            (object)['cConf' => 'N'],
            true
        );
        $this->setConfig('ftp_hostname', 'localhost', \CONF_FS, 'FTP Hostname', 'text', 101, null, true);
        $this->setConfig('ftp_port', '21', \CONF_FS, 'FTP Port', 'number', 102, null, true);
        $this->setConfig('ftp_user', '', \CONF_FS, 'FTP Benutzer', 'text', 103, null, true);
        $this->setConfig('ftp_pass', '', \CONF_FS, 'FTP Passwort', 'pass', 104, null, true);
        $this->setConfig(
            'ftp_ssl',
            'N',
            \CONF_FS,
            'FTP SSL',
            'selectbox',
            105,
            (object)[
                'cBeschreibung' => 'Verschlüsselte Verbindung aktivieren?',
                'inputOptions'  => [
                    '1' => 'Ja',
                    '0' => 'Nein',
                ],
            ],
            true
        );
        $this->setConfig(
            'ftp_path',
            '/',
            \CONF_FS,
            'FTP Pfad',
            'text',
            106,
            (object)['cBeschreibung' => 'Pfad zum Shop Hauptverzeichnis?'],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('ftp_header');
        $this->removeConfig('ftp_hostname');
        $this->removeConfig('ftp_port');
        $this->removeConfig('ftp_user');
        $this->removeConfig('ftp_pass');
        $this->removeConfig('ftp_ssl');
        $this->removeConfig('ftp_path');
    }
}
