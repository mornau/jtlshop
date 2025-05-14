<?php

declare(strict_types=1);

namespace JTL\CSV;

use JTL\DB\DbInterface;
use JTL\Helpers\URL;
use JTL\Language\LanguageHelper;
use JTL\Redirect;

class RedirectImporter extends Importer
{
    /**
     * @var string[]
     */
    protected array $errors = [];

    protected string $defLangIso = '';

    public function __construct(DbInterface $db)
    {
        parent::__construct($db);
        $this->setTargetTable('tredirect');
        $this->addFieldNameMapping('sourceurl', 'cFromUrl');
        $this->addFieldNameMapping('destinationurl', 'cToUrl');
        $this->addFieldNameMapping('articlenumber', 'cArtNr');
        $this->addFieldNameMapping('languageiso', 'cIso');
        $this->setFromUserInput();
        $this->defLangIso = LanguageHelper::getDefaultLanguage()->getCode();
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    protected function verifyFieldNames(array $fieldNames): bool
    {
        if (!\in_array('cFromUrl', $fieldNames, true)) {
            $this->errors[] = \__('csvImportNoSourceUrl');

            return false;
        }
        if (!\in_array('cToUrl', $fieldNames, true) && !\in_array('cArtNr', $fieldNames, true)) {
            $this->errors[] = \__('csvImportNoArtNrOrDestUrl');

            return false;
        }
        if (\in_array('cToUrl', $fieldNames, true) && \in_array('cArtNr', $fieldNames, true)) {
            $this->errors[] = \__('csvImportArtNrAndDestUrlError');

            return false;
        }

        return true;
    }

    private function getProductUrlByArtNr(string $artNr, string $langIso): ?string
    {
        $item = $this->db->getSingleObject(
            "SELECT tartikel.kArtikel, tseo.cSeo
                FROM tartikel
                LEFT JOIN tsprache ON tsprache.cISO = :iso
                LEFT JOIN tseo
                    ON tseo.cKey = 'kArtikel'
                    AND tseo.kKey = tartikel.kArtikel
                    AND tseo.kSprache = tsprache.kSprache
                WHERE tartikel.cArtNr = :artNr",
            ['iso' => \mb_strtolower($langIso), 'artNr' => $artNr]
        );

        if ($item === null) {
            return null;
        }

        return URL::buildURL($item, \URLART_ARTIKEL);
    }

    protected function preprocessObject(object $obj, int $lineNum): bool
    {
        if (isset($obj->cArtNr)) {
            $obj->cIso   = $obj->cIso ?? $this->defLangIso;
            $obj->cToUrl = $this->getProductUrlByArtNr($obj->cArtNr, $obj->cIso);

            if (empty($obj->cToUrl)) {
                $this->errors[] = \sprintf(\__('csvImportArtNrNotFound'), $obj->cArtNr);

                return false;
            }

            unset($obj->cArtNr, $obj->cIso);
        }

        $parsed = \parse_url($obj->cFromUrl);
        $from   = $parsed['path'];

        if (isset($parsed['query'])) {
            $from .= '?' . $parsed['query'];
        }

        $obj->cFromUrl = $from;

        return true;
    }

    protected function importObject(object $obj, int $lineNum): bool
    {
        $redirect = new Redirect(0, $this->db);
        $result   = $redirect->saveExt($obj->cFromUrl, $obj->cToUrl, false, 0, $this->overwriteExisting);

        if ($result === false) {
            $this->errors[] = \sprintf(\__('csvImportSaveError'), $lineNum);

            return false;
        }

        return true;
    }
}
