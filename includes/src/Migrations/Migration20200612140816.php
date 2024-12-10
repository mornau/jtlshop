<?php

/**
 * Remove path from redirects
 *
 * @author fp
 * @created Fri, 12 Jun 2020 14:08:16 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Shop;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200612140816
 */
class Migration20200612140816 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Remove path from redirects';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $shopSubPath = \trim(\parse_url(Shop::getURL(), PHP_URL_PATH) ?: '', '/') . '/';
        if (\strlen($shopSubPath) > 1) {
            // remove Shop-URL path from redirection source
            $this->db->queryPrepared(
                "UPDATE tredirect
                    SET cFromUrl = REPLACE(cFromUrl, :path, '')
                    WHERE cFromUrl LIKE :searchPath",
                [
                    'searchPath' => '/' . $shopSubPath . '%',
                    'path'       => $shopSubPath
                ]
            );
            // delete all redirects where source and destination are equal
            $this->execute('DELETE FROM tredirect WHERE cFromUrl = cToUrl');
            // delete not found records with existing redirection
            $this->execute(
                "DELETE t1 FROM tredirect t1
                    INNER JOIN tredirect t2 ON t2.cFromUrl = t1.cFromUrl
                                           AND t2.kRedirect != t1.kRedirect
                    WHERE t1.cToUrl = '';"
            );
            // delete all duplicate redirects
            $this->execute(
                'DELETE t1 FROM tredirect t1
                    INNER JOIN tredirect t2 ON t2.cFromUrl = t1.cFromUrl
                                           AND t2.kRedirect > t1.kRedirect;'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
