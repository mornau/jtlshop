<?php

declare(strict_types=1);

namespace JTL\Backend\LocalizationCheck;

use Illuminate\Support\Collection;
use JTL\DB\DbInterface;
use JTL\Language\LanguageModel;

/**
 * Class AbstractLocalizationCheck
 * @package JTL\Backend\LocalizationCheck
 */
abstract class AbstractLocalizationCheck implements LocalizationCheckInterface
{
    /**
     * @var Collection<string>
     */
    protected Collection $activeLanguageCodes;

    /**
     * @var Collection<int>
     */
    protected Collection $activeLanguageIDs;

    /**
     * @var Collection<LanguageModel>
     */
    protected Collection $nonDefaultLanguages;

    /**
     * @param DbInterface               $db
     * @param Collection<LanguageModel> $activeLanguages
     */
    public function __construct(protected DbInterface $db, protected Collection $activeLanguages)
    {
        $this->activeLanguageIDs   = $activeLanguages->map(static function (LanguageModel $model): int {
            return $model->getId();
        });
        $this->activeLanguageCodes = $activeLanguages->map(static function (LanguageModel $model): string {
            return $model->getCode();
        });
        $this->nonDefaultLanguages = $this->activeLanguages->filter(static function (LanguageModel $model): bool {
            return !$model->isShopDefault();
        });
    }

    /**
     * @return DbInterface
     */
    public function getDB(): DbInterface
    {
        return $this->db;
    }

    /**
     * @param DbInterface $db
     */
    public function setDB(DbInterface $db): void
    {
        $this->db = $db;
    }

    /**
     * @return Collection<LanguageModel>
     */
    public function getActiveLanguages(): Collection
    {
        return $this->activeLanguages;
    }

    /**
     * @param Collection<LanguageModel> $activeLanguages
     */
    public function setActiveLanguages(Collection $activeLanguages): void
    {
        $this->activeLanguages = $activeLanguages;
    }

    /**
     * @return Collection<int>
     */
    public function getActiveLanguageIDs(): Collection
    {
        return $this->activeLanguageIDs;
    }

    /**
     * @param Collection<int> $activeLanguageIDs
     */
    public function setActiveLanguageIDs(Collection $activeLanguageIDs): void
    {
        $this->activeLanguageIDs = $activeLanguageIDs;
    }

    /**
     * @return Collection<string>
     */
    public function getActiveLanguageCodes(): Collection
    {
        return $this->activeLanguageCodes;
    }

    /**
     * @param Collection<string> $activeLanguageCodes
     */
    public function setActiveLanguageCodes(Collection $activeLanguageCodes): void
    {
        $this->activeLanguageCodes = $activeLanguageCodes;
    }

    /**
     * @return Collection<LanguageModel>
     */
    public function getNonDefaultLanguages(): Collection
    {
        return $this->nonDefaultLanguages;
    }

    /**
     * @param Collection<LanguageModel> $nonDefaultLanguages
     */
    public function setNonDefaultLanguages(Collection $nonDefaultLanguages): void
    {
        $this->nonDefaultLanguages = $nonDefaultLanguages;
    }
}
