<?php

declare(strict_types=1);

namespace JTL\ServiceReport\Report;

use JTL\Backend\FileCheck;
use JTL\Backend\Status;
use JTL\Shop;

class ShopStatus implements ReportInterface
{
    /**
     * @return array{dbPhpTimeDiff: array{db: string, php: string, diff: int}, hasActiveProfiler: bool,
     *      hasDifferentTemplateVersion: bool,
     *      hasExtensionSOAP: bool, hasFullTextIndexError: bool, hasInsecureMailConfig: bool,
     *      hasInstalledStandardLang: bool, hasInstallDir: bool, hasNewPluginVersions: bool,
     *      hasOrphanedCategories: bool, hasPendingUpdates: bool, hasStandardTemplateIssue: bool,
     *      hasValidEnvironment: bool, validDBStructure: bool, validFolderPermissions: bool,
     *      validModifiedFileStruct: bool, validOrphanedFilesStruct: bool}
     * @throws \Exception
     */
    public function getData(): array
    {
        $status = Status::getInstance(Shop::Container()->getDB(), Shop::Container()->getCache());

        return [
            'validDBStructure'            => $status->validDatabaseStruct(),
            'validFolderPermissions'      => $status->validFolderPermissions(),
            'validModifiedFileStruct'     => $status->validModifiedFileStruct(),
            'modifiedFiles'               => $this->checkModifiedFiles(),
            'validOrphanedFilesStruct'    => $status->validOrphanedFilesStruct(),
            'hasNewPluginVersions'        => $status->hasNewPluginVersions(),
            'hasValidEnvironment'         => $status->hasValidEnvironment(),
            'hasActiveProfiler'           => $status->hasActiveProfiler(),
            'hasPendingUpdates'           => $status->hasPendingUpdates(),
            'hasExtensionSOAP'            => $status->hasExtensionSOAP(),
            'hasDifferentTemplateVersion' => $status->hasDifferentTemplateVersion(),
            'hasInstallDir'               => $status->hasInstallDir(),
            'hasFullTextIndexError'       => $status->hasFullTextIndexError(),
            'hasInsecureMailConfig'       => $status->hasInsecureMailConfig(),
            'hasInstalledStandardLang'    => $status->hasInstalledStandardLang(),
            'hasOrphanedCategories'       => $status->hasOrphanedCategories(),
            'hasStandardTemplateIssue'    => $status->hasStandardTemplateIssue(),
            'dbPhpTimeDiff'               => $status->hasMysqlPhpTimeMismatch(),
        ];
    }

    /**
     * @return string[]
     */
    private function checkModifiedFiles(): array
    {
        $fileCheck          = new FileCheck();
        $modifiedFilesCount = 0;
        $modifiedFiles      = [];
        $coreMD5HashFile    = \PFAD_ROOT . \PFAD_ADMIN . \PFAD_INCLUDES . \PFAD_SHOPMD5
            . $fileCheck->getVersionString() . '.csv';
        $fileCheck->validateCsvFile($coreMD5HashFile, $modifiedFiles, $modifiedFilesCount);

        return $modifiedFiles;
    }
}
