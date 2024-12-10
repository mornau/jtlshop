<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201218142100
 */
class Migration20201218142100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add subject for MAILTEMPLATE_CHECKBOX_SHOPBETREIBER';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $tplID = $this->getDB()->getSingleObject(
            'SELECT kEmailvorlage AS id
                FROM temailvorlage
                WHERE cModulId = :nm',
            ['nm' => MAILTEMPLATE_CHECKBOX_SHOPBETREIBER]
        );
        if ($tplID === null) {
            return;
        }
        $tplID = (int)$tplID->id;
        $this->execute(
            'UPDATE `temailvorlagespracheoriginal` 
                SET `cBetreff` = \'Auswahl einer Checkboxoption\' 
                WHERE `kEmailvorlage` = ' . $tplID . ' AND `kSprache` = 1 AND cBetreff = \'\''
        );
        $this->execute(
            'UPDATE `temailvorlagespracheoriginal`
                SET `cBetreff` = \'Checkbox option checked\' 
                WHERE `kEmailvorlage` = ' . $tplID . ' AND `kSprache` = 2 AND cBetreff = \'\''
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
