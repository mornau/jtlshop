<?php

/**
 * add_nfehlerhaft_texportformat
 *
 * @author mh
 * @created Wed, 03 Apr 2019 11:55:19 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JsonException;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190403115519
 */
class Migration20190403115519 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add nFehlerhaft to texportformat, tpluginemailvorlage';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $id = $this->getDB()->getSingleObject(
            "SELECT kEmailvorlage 
                FROM temailvorlage 
                WHERE cModulId = 'core_jtl_rma_submitted'"
        );
        if ($id !== null) {
            $this->getDB()->delete('temailvorlage', 'kEmailvorlage', $id->kEmailvorlage);
            $this->getDB()->delete('temailvorlagesprache', 'kEmailvorlage', $id->kEmailvorlage);
            $this->getDB()->delete('temailvorlagespracheoriginal', 'kEmailvorlage', $id->kEmailvorlage);
        }
        $revs = $this->getDB()->selectAll('trevisions', 'type', 'mail');
        foreach ($revs as $rev) {
            $update = false;
            try {
                /** @var \stdClass $content */
                $content = \json_decode($rev->content, false, 512, \JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                continue;
            }
            if (isset($content->references)) {
                /** @var \stdClass $ref */
                foreach ((array)$content->references as $ref) {
                    if (isset($ref->cDateiname)) {
                        $update         = true;
                        $ref->cPDFNames = $ref->cDateiname;
                        unset($ref->cDateiname);
                    }
                }
            }
            if ($update === true) {
                try {
                    $rev->content = \json_encode($content, \JSON_THROW_ON_ERROR);
                } catch (JsonException) {
                    continue;
                }
                $rev->reference_secondary = $rev->reference_secondary ?? '_DBNULL_';
                $this->getDB()->update('trevisions', 'id', $rev->id, $rev);
            }
        }
        $this->execute("UPDATE temailvorlagesprache SET cBetreff = '' WHERE kEmailvorlage > 0 AND cBetreff IS NULL");
        $this->execute(
            "UPDATE temailvorlagespracheoriginal 
            SET cBetreff = '' WHERE kEmailvorlage > 0 AND cBetreff IS NULL"
        );
        $this->execute('DELETE FROM texportformat WHERE nSpecial = 1 AND kPlugin = 0');
        $this->execute('ALTER TABLE texportformat ADD COLUMN nFehlerhaft TINYINT(1) DEFAULT 0');
        $this->execute('ALTER TABLE tpluginemailvorlage ADD COLUMN nFehlerhaft TINYINT(1) DEFAULT 0');
        $this->execute(
            'ALTER TABLE temailvorlagesprache 
            CHANGE COLUMN `cDateiname` `cPDFNames` VARCHAR(255) NULL DEFAULT NULL'
        );
        $this->execute(
            'ALTER TABLE temailvorlagespracheoriginal 
            CHANGE COLUMN `cDateiname` `cPDFNames` VARCHAR(255) NULL DEFAULT NULL'
        );
        $this->execute(
            'ALTER TABLE tpluginemailvorlagesprache 
            CHANGE COLUMN `cDateiname` `cPDFNames` VARCHAR(255) NULL DEFAULT NULL'
        );
        $this->execute(
            'ALTER TABLE tpluginemailvorlagespracheoriginal
            CHANGE COLUMN `cDateiname` `cPDFNames` VARCHAR(255) NULL DEFAULT NULL'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE texportformat DROP COLUMN nFehlerhaft');
        $this->execute('ALTER TABLE tpluginemailvorlage DROP COLUMN nFehlerhaft');
        $this->execute(
            'ALTER TABLE temailvorlagesprache 
            CHANGE COLUMN `cPDFNames` `cDateiname` VARCHAR(255) NULL DEFAULT NULL'
        );
        $this->execute(
            'ALTER TABLE temailvorlagespracheoriginal 
            CHANGE COLUMN `cPDFNames` `cDateiname` VARCHAR(255) NULL DEFAULT NULL'
        );
        $this->execute(
            'ALTER TABLE tpluginemailvorlagesprache 
            CHANGE COLUMN `cPDFNames` `cDateiname` VARCHAR(255) NULL DEFAULT NULL'
        );
        $this->execute(
            'ALTER TABLE tpluginemailvorlagespracheoriginal 
            CHANGE COLUMN `cPDFNames` `cDateiname` VARCHAR(255) NULL DEFAULT NULL'
        );
    }
}
