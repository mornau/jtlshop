<?php

/**
 * transfer nl subscribers into optin table
 *
 * @author cr
 * @created Tue, 04 Jun 2019 12:27:45 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Optin\OptinNewsletter;
use JTL\Optin\OptinRefData;
use JTL\Shop;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190604122745
 */
class Migration20190604122745 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Transfer NL subscribers into optin table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $nlSubscribers = $this->getDB()->getObjects('SELECT * FROM tnewsletterempfaenger WHERE nAktiv = 1');
        foreach ($nlSubscribers as $subscriber) {
            $languageID = (int)($subscriber->kSprache ?? 0);
            if ($languageID === 0) {
                $languageID = Shop::getLanguageID();
            }

            $refData = (new OptinRefData())
                ->setOptinClass(OptinNewsletter::class)
                ->setLanguageID($languageID)
                ->setSalutation($subscriber->cAnrede ?? '')
                ->setFirstName($subscriber->cVorname ?? '')
                ->setLastName($subscriber->cNachname ?? '')
                ->setEmail($subscriber->cEmail ?? '')
                ->setCustomerID((int)$subscriber->kKunde);

            $this->getDB()->queryPrepared(
                'INSERT INTO toptin(
                    kOptinCode,
                    kOptinClass,
                    cMail,
                    cRefData,
                    dCreated,
                    dActivated
                )
                VALUES(
                    :optCode,
                    :optinNewsletter,
                    :email,
                    :refData,
                    :eingetragen,
                    NOW()
                )
                ON DUPLICATE KEY UPDATE
                     kOptinClass = kOptinClass,
                     cMail = cMail,
                     cRefData = cRefdata,
                     dCreated = NOW(),
                     dActivated = NOW()',
                [
                    'optCode' => $subscriber->cOptCode,
                    'optinNewsletter' => \quotemeta(OptinNewsletter::class),
                    'email' => $subscriber->cEmail,
                    'refData' => \quotemeta(\serialize($refData)),
                    'eingetragen' => $subscriber->dEingetragen
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM toptin WHERE kOptinClass = '" . \quotemeta(OptinNewsletter::class) . "'");
    }
}
