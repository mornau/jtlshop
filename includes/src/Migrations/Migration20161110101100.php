<?php

/**
 * change input types to password
 *
 * @author fm
 * @created Wed, 10 Nov 2016 10:11:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161110101100
 */
class Migration20161110101100 extends Migration implements IMigration
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
        $this->execute("UPDATE teinstellungenconf SET cInputTyp = 'pass' WHERE cWertName = 'newsletter_smtp_pass'");
        $this->execute("UPDATE teinstellungenconf SET cInputTyp = 'pass' WHERE cWertName = 'caching_redis_pass'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE teinstellungenconf SET cInputTyp = 'text' WHERE cWertName = 'newsletter_smtp_pass'");
        $this->execute("UPDATE teinstellungenconf SET cInputTyp = 'text' WHERE cWertName = 'caching_redis_pass'");
    }
}
