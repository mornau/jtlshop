<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180919103846
 */
class Migration20180919103846 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Add anonymizing settings';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('global_ips_speichern');
        $this->removeConfig('bestellabschluss_ip_speichern');
        $cronDataProtection = $this->fetchArray("SELECT * FROM tcron WHERE cJobArt = 'dataprotection'");
        if (count($cronDataProtection) > 0) {
            $this->execute(
                "INSERT INTO tcron(kKey, cKey, cJobArt, nAlleXStd,cTabelle, cName, dStart, dStartZeit, dLetzterStart)
                    VALUES(50, '', 'dataprotection', 24, '', '', NOW(), '00:00:00', NOW())"
            );
        }
        $this->execute(
            "CREATE TABLE IF NOT EXISTS tanondatajournal(
                kAnonDatenHistory INT(11) NOT NULL AUTO_INCREMENT,
                cIssuer VARCHAR(255) DEFAULT '' COMMENT 'application(cron), user, admin',
                iIssuerId INT(11) DEFAULT NULL COMMENT 'id of the issuer (only for user or admin)',
                dEventTime DATETIME DEFAULT NULL COMMENT 'time of the event',
                PRIMARY KEY kAnonDatenHistory(kAnonDatenHistory),
                KEY kIssuer(iIssuerId)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE tanondatajournal');
        $this->execute("DELETE FROM tcron WHERE cJobArt = 'dataprotection'");
        $this->execute("DELETE FROM tjobqueue WHERE cJobArt = 'dataprotection'");
        $this->execute(
            "INSERT INTO teinstellungenconf VALUES
                (335, 1, 'IP-Adresse bei Bestellung mitspeichern',
                 'Soll die IP-Adresse des Kunden in der Datenbank gespeichert werden, "
            . "wenn er eine Bestellung abschliesst?',
                 'bestellabschluss_ip_speichern', 'selectbox', NULL, 554, 1, 0, 'Y'),
                (1133, 1 ,'IPs speichern',
                 'Sollen IPs von Benutzern bei z.b. Umfragen, Tags etc. als Floodschutz oder sonstigen "
            . "Trackingm&ouml;glichkeiten gespeichert werden?' ,'global_ips_speichern',
                'selectbox', NULL, 552, 1, 0 , 'Y')"
        );
        $this->execute(
            "INSERT INTO teinstellungenconfwerte VALUE
                ('335','Ja','Y','1'),
                ('335','Nein','N','2'),
                ('1133','Ja','Y','1'),
                ('1133','Nein','N','2')"
        );
    }
}
