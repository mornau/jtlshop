<?php

/**
 * Convert encrypted data to utf-8
 *
 * @author fp
 * @created Tue, 09 Jan 2018 10:46:08 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Helpers\Text;
use JTL\Shop;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180109104608
 */
class Migration20180109104608 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Convert encrypted data to utf-8';
    }

    private const PROPERTIES = [
        'tkunde'            => ['kKunde', 'cNachname', 'cFirma', 'cZusatz', 'cStrasse'],
        'tzahlungsinfo'     => [
            'kZahlungsInfo',
            'cBankName',
            'cKartenNr',
            'cCVV',
            'cKontoNr',
            'cBLZ',
            'cIBAN',
            'cBIC',
            'cInhaber',
            'cVerwendungszweck'
        ],
        'tkundenkontodaten' => ['kKundenKontodaten', 'cBankName', 'nKonto', 'cBLZ', 'cIBAN', 'cBIC', 'cInhaber'],
    ];

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $cryptoService = Shop::Container()->getCryptoService();
        foreach (self::PROPERTIES as $tableName => $propNames) {
            $keyName = \array_shift($propNames);
            $dataSet = $this->fetchAll(
                'SELECT ' . $keyName . ', ' . \implode(', ', $propNames)
                . ' FROM ' . $tableName
            );

            foreach ($dataSet as $dataObj) {
                foreach ($propNames as $propName) {
                    if ($dataObj->$propName === null) {
                        continue;
                    }
                    $dataObj->$propName = $cryptoService->decryptXTEA($dataObj->$propName);
                    if (!Text::is_utf8($dataObj->$propName)) {
                        $dataObj->$propName = Text::convertUTF8($dataObj->$propName);
                    }
                    $dataObj->$propName = $cryptoService->encryptXTEA($dataObj->$propName);
                }

                $this->getDB()->update($tableName, $keyName, $dataObj->$keyName, $dataObj);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $cryptoService = Shop::Container()->getCryptoService();
        foreach (self::PROPERTIES as $tableName => $propNames) {
            $keyName = \array_shift($propNames);
            $dataSet = $this->fetchAll(
                'SELECT ' . $keyName . ', ' . \implode(', ', $propNames)
                . ' FROM ' . $tableName
            );
            foreach ($dataSet as $dataObj) {
                foreach ($propNames as $propName) {
                    if ($dataObj->$propName === null) {
                        continue;
                    }
                    $dataObj->$propName = $cryptoService->decryptXTEA($dataObj->$propName);
                    if (Text::is_utf8($dataObj->$propName)) {
                        $dataObj->$propName = Text::convertISO($dataObj->$propName);
                    }
                    $dataObj->$propName = $cryptoService->encryptXTEA($dataObj->$propName);
                }

                $this->getDB()->update($tableName, $keyName, $dataObj->$keyName, $dataObj);
            }
        }
    }
}
