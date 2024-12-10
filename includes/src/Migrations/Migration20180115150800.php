<?php

/**
 * update tsynclogin table
 *
 * @author fm
 * @created Mon, 15 Jan 2018 15:08:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180115150800
 */
class Migration20180115150800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Update tsynclogin table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $values = $this->getDB()->select('tsynclogin', [], []);

        $this->execute('DELETE FROM `tsynclogin`');
        $this->execute(
            'ALTER TABLE `tsynclogin`
                ADD COLUMN `kSynclogin` INT NOT NULL DEFAULT 1 FIRST,
                ADD PRIMARY KEY (`kSynclogin`)'
        );
        $this->execute(
            "ALTER TABLE `tsynclogin`
                CHANGE COLUMN `cMail` `cMail` VARCHAR(255) NULL DEFAULT '',
                CHANGE COLUMN `cName` `cName` VARCHAR(255) NOT NULL,
                CHANGE COLUMN `cPass` `cPass` VARCHAR(255) NOT NULL"
        );
        if ($values === null) {
            return;
        }

        $values->kSynclogin = 1;
        $passInfo           = \password_get_info($values->cPass);
        // PHP7.3 => (int)0, PHP7.4++ => NULL
        if (empty($passInfo['algo'])) {
            $values->cPass = \password_hash($values->cPass, PASSWORD_DEFAULT);
        }

        $this->getDB()->insert('tsynclogin', $values);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $columns = $this->getDB()->getSingleObject("SHOW COLUMNS FROM tsynclogin LIKE 'kSynclogin'");
        if ($columns !== null && $columns->Field === 'kSynclogin') {
            $this->execute(
                'ALTER TABLE `tsynclogin`
                    DROP COLUMN `kSynclogin`,
                    DROP PRIMARY KEY'
            );
        }
    }
}
