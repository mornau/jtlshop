<?php

/**
 * correcting toptin quotemeta
 *
 * @author cr
 * @created Fri, 12 Mar 2021 14:21:36 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210312142136
 */
class Migration20210312142136 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Fix toptin quotemeta';
    }

    /**
     * @param string $instr
     * @return string
     */
    private function stripquotes(string $instr): string
    {
        $replacements = ['.', '\\', '+', '*', '?', '[', '^', ']', '(', '$', ')'];
        $pattern      = ['\.', '\\\\', '\+', '\*', '\?', '\[', '\^', '\]', '\(', '\$', '\)'];

        return \str_replace($pattern, $replacements, $instr);
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        foreach ($this->getDB()->getObjects('SELECT * FROM toptin') as $optin) {
            $optin->kOptinClass = $this->stripquotes($optin->kOptinClass);
            $optin->cRefData    = $this->stripquotes($optin->cRefData);
            $this->getDB()->queryPrepared(
                'UPDATE toptin
                    SET kOptinClass = :kOptinClass,
                        cRefData = :cRefData
                    WHERE kOptin = :kOptin',
                [
                    'kOptinClass' => $optin->kOptinClass,
                    'cRefData'    => $optin->cRefData,
                    'kOptin'      => $optin->kOptin
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        // there is no way back
    }
}
