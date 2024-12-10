<?php

/**
 * Create index for statistics
 *
 * @author fp
 * @created Tue, 02 Nov 2021 09:33:40 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20211102093340
 */
class Migration20211102093340 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /** @lang text */
    public function getDescription(): string
    {
        return 'Create index for statistics';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        if ($this->fetchOne("SHOW INDEX FROM tbesucherarchiv WHERE KEY_NAME = 'idx_kBot_dZeit'")) {
            $this->execute('DROP INDEX idx_kBot_dZeit ON tbesucherarchiv');
        }
        if ($this->fetchOne("SHOW INDEX FROM tbesucher WHERE KEY_NAME = 'idx_kBot_dZeit'")) {
            $this->execute('DROP INDEX idx_kBot_dZeit ON tbesucher');
        }
        $this->execute('ALTER TABLE tbesucherarchiv ADD INDEX idx_kBot_dZeit (kBesucherBot, dZeit)');
        $this->execute('ALTER TABLE tbesucher ADD INDEX idx_kBot_dZeit (kBesucherBot, dZeit)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP INDEX idx_kBot_dZeit ON tbesucherarchiv');
        $this->execute('DROP INDEX idx_kBot_dZeit ON tbesucher');
    }
}
