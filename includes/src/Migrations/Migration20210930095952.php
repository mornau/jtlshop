<?php

/**
 * Create double optin for avail again
 *
 * @author fp
 * @created Thu, 30 Sep 2021 09:59:52 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Catalog\Product\Artikel;
use JTL\Optin\Optin;
use JTL\Optin\OptinAvailAgain;
use JTL\Optin\OptinRefData;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210930095952
 */
class Migration20210930095952 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create double optin for avail again';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $subscriptions                       = $this->db->getObjects(
            'SELECT vfb.kVerfuegbarkeitsbenachrichtigung, vfb.kArtikel, vfb.kSprache,
                vfb.cVorname, vfb.cNachname, vfb.cMail, vfb.dErstellt
                FROM tverfuegbarkeitsbenachrichtigung AS vfb
                INNER JOIN (
                    SELECT MIN(tmigration.dExecuted) AS dDate
                    FROM tmigration
                    WHERE tmigration.nVersion >= 500) AS min500
                    ON min500.dDate > vfb.dErstellt
                WHERE vfb.nStatus = 0
                    AND (vfb.dBenachrichtigtAm IS NULL)'
        );
        $options                             = Artikel::getDefaultOptions();
        $options->nKeineSichtbarkeitBeachten = 1;
        $this->db->commit();
        foreach ($subscriptions as $subscription) {
            if (empty($subscription->cMail)) {
                continue;
            }
            $product = (new Artikel($this->db))->fuelleArtikel((int)$subscription->kArtikel, $options);
            if ($product === null || empty($product->kArtikel)) {
                continue;
            }
            /** @var OptinAvailAgain $availAgainOptin */
            $availAgainOptin = (new Optin(OptinAvailAgain::class))->getOptinInstance();
            $availAgainOptin->setProduct($product)
                ->setEmail($subscription->cMail);
            if ($availAgainOptin->isActive()) {
                continue;
            }
            $refData = (new OptinRefData())
                ->setSalutation('')
                ->setFirstName($subscription->cVorname ?? '')
                ->setLastName($subscription->cNachname ?? '')
                ->setProductId($product->kArtikel)
                ->setEmail($subscription->cMail)
                ->setLanguageID((int)$subscription->kSprache)
                ->setOptinClass(OptinAvailAgain::class)
                ->setRealIP('');
            $this->db->insert(
                'toptin',
                (object)[
                    'kOptinCode'  => 'Migration_20210930095952_' . $subscription->kVerfuegbarkeitsbenachrichtigung,
                    'kOptinClass' => OptinAvailAgain::class,
                    'cMail'       => $subscription->cMail,
                    'cRefData'    => \serialize($refData),
                    'dCreated'    => 'now()',
                    'dActivated'  => 'now()',
                ]
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
