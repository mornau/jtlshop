<?php

/**
 * old object cache settings
 *
 * @author fm
 * @created Th, 7 Apr 2016 11:14:23 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160407111423
 */
class Migration20160407111423 extends Migration implements IMigration
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
        $this->execute('DELETE FROM `teinstellungenconf` WHERE kEinstellungenSektion = 123;');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO `teinstellungenconf` VALUES 
(1537,123,'Object Caching aktivieren','Soll das Object Caching aktiviert werden?',
 'object_caching_activated','selectbox','',1,1,0,'Y'),
(1538,123,'Object Caching Methode','Welche Methode soll für das Object Caching benutzt werden?',
 'object_caching_method','selectbox','',5,1,0,'Y'),
(1539,123,'Memcached Hostname','Hostname für den Memcached Server','object_caching_memcached_host',
 'text','',15,1,0,'Y'),
(1540,123,'Memcached Port','Port für den Memcached Server','object_caching_memcached_port',
 'zahl','',20,1,0,'Y'),
(1541,123,'Object Caching Debug Modus','Soll der Debug Modus aktiviert werden?','object_caching_debug_mode',
 'selectbox','',25,1,0,'Y')"
        );
    }
}
