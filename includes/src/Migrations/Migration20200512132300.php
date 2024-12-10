<?php

/**Added isAdmin, parentCommentID columns to tnewskommentar table*/

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200512132300
 */
class Migration20200512132300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'je';
    }

    public function getDescription(): string
    {
        return 'Added isAdmin, parentCommentID columns to tnewskommentar table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE `tnewskommentar`
                ADD `parentCommentID` int(10) unsigned NOT NULL DEFAULT '0' AFTER `cKommentar`,
                ADD `isAdmin` int(10) unsigned NOT NULL DEFAULT '0' AFTER `parentCommentID`"
        );
        $this->setLocalization('ger', 'news', 'commentReply', 'Antwort');
        $this->setLocalization('eng', 'news', 'commentReply', 'Reply');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('commentReply', 'news');
        $this->execute(
            'ALTER TABLE `tnewskommentar` DROP `isAdmin`, DROP `parentCommentID`'
        );
    }
}
