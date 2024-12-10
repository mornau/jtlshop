<?php

declare(strict_types=1);

namespace JTL\Backend;

use DateTime;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use JTL\Backend\LocalizationCheck\LocalizationCheckFactory;
use JTL\Backend\LocalizationCheck\Result;
use JTL\Cache\JTLCacheInterface;
use JTL\Checkout\ZahlungsLog;
use JTL\DB\DbInterface;
use JTL\DB\Migration\Info;
use JTL\DB\Migration\Structure;
use JTL\Export\Validator;
use JTL\Language\LanguageHelper;
use JTL\Language\LanguageModel;
use JTL\License\Manager;
use JTL\License\Mapper;
use JTL\Mail\Template\Model as MailTplModel;
use JTL\Mail\Template\TemplateFactory;
use JTL\Media\Image\Product;
use JTL\Media\Image\StatsItem;
use JTL\Nice;
use JTL\Plugin\Helper;
use JTL\Plugin\State;
use JTL\Profiler;
use JTL\Settings\Option\Globals;
use JTL\Settings\Option\Overview;
use JTL\Settings\Settings;
use JTL\Shop;
use JTL\Update\Updater;
use stdClass;
use Systemcheck\Environment;
use Systemcheck\Platform\Filesystem;
use Systemcheck\Platform\Hosting;
use Systemcheck\Platform\PDOConnection;
use Systemcheck\Tests\AbstractTest;

use function Functional\some;

/**
 * Class Status
 * @package JTL\Backend
 */
class Status
{
    /**
     * @var self|null
     */
    private static ?Status $instance = null;

    public const CACHE_ID_FOLDER_PERMISSIONS   = 'validFolderPermissions';
    public const CACHE_ID_DATABASE_STRUCT      = 'validDatabaseStruct';
    public const CACHE_ID_MODIFIED_FILE_STRUCT = 'validModifiedFileStruct';
    public const CACHE_ID_ORPHANED_FILE_STRUCT = 'validOrphanedFilesStruct';
    public const CACHE_ID_EMAIL_SYNTAX_CHECK   = 'validEMailSyntaxCheck';
    public const CACHE_ID_EXPORT_SYNTAX_CHECK  = 'validExportSyntaxCheck';

    /**
     * Status constructor.
     * @param DbInterface       $db
     * @param JTLCacheInterface $cache
     */
    public function __construct(protected DbInterface $db, protected JTLCacheInterface $cache)
    {
        self::$instance = $this;
    }

    /**
     * @param DbInterface            $db
     * @param JTLCacheInterface|null $cache
     * @param bool                   $flushCache
     * @return self
     */
    public static function getInstance(
        DbInterface $db,
        ?JTLCacheInterface $cache = null,
        bool $flushCache = false
    ): self {
        $instance = self::$instance ?? new self($db, $cache ?? Shop::Container()->getCache());
        if ($flushCache) {
            $instance->cache->flushTags([\CACHING_GROUP_STATUS]);
        }

        return $instance;
    }

    /**
     * @return JTLCacheInterface
     */
    public function getObjectCache(): JTLCacheInterface
    {
        return $this->cache->setJtlCacheConfig(
            $this->db->selectAll('teinstellungen', 'kEinstellungenSektion', \CONF_CACHING)
        );
    }

    /**
     * @return StatsItem
     * @throws Exception
     */
    public function getImageCache(): StatsItem
    {
        return (new Product($this->db))->getStats();
    }

    /**
     * @return object&stdClass{error: bool, notice: bool, debug: bool}
     */
    public function getSystemLogInfo(): stdClass
    {
        $conf = Settings::intValue(Globals::SYSLOG_LEVEL);

        return (object)[
            'error'  => $conf >= \JTLLOG_LEVEL_ERROR,
            'notice' => $conf >= \JTLLOG_LEVEL_NOTICE,
            'debug'  => $conf >= \JTLLOG_LEVEL_NOTICE
        ];
    }

    /**
     * checks the db-structure against 'admin/includes/shopmd5files/dbstruct_[shop-version].json'
     *
     * @return bool
     */
    public function validDatabaseStruct(): bool
    {
        $info   = new Info($this->db);
        $struct = new Structure($this->db, $this->cache, $info);
        /** @var array{current: array<string, stdClass>, original: array<string, array<int, string>>}|false $dbStruct */
        $dbStruct = $this->cache->get(self::CACHE_ID_DATABASE_STRUCT);
        if ($dbStruct === false) {
            try {
                $fileStruct = $struct->getDBFileStruct();
            } catch (InvalidArgumentException) {
                $fileStruct = [];
            }
            $dbStruct = [
                'current'  => $struct->getDBStruct(true),
                'original' => $fileStruct
            ];
            $this->cache->set(self::CACHE_ID_DATABASE_STRUCT, $dbStruct, [\CACHING_GROUP_STATUS]);
        }

        return \is_array($dbStruct['current'])
            && \is_array($dbStruct['original'])
            && \count($struct->compareDBStruct($dbStruct['original'], $dbStruct['current'])) === 0;
    }

    /**
     * checks the shop-filesystem-structure against 'admin/includes/shopmd5files/[shop-version].csv'
     *
     * @param string|null $hash
     * @return bool  true='no errors', false='something is wrong'
     */
    public function validModifiedFileStruct(?string &$hash = null): bool
    {
        if (($struct = $this->cache->get(self::CACHE_ID_MODIFIED_FILE_STRUCT)) === false) {
            $check   = new FileCheck();
            $files   = [];
            $stats   = 0;
            $md5file = \PFAD_ROOT . \PFAD_ADMIN . \PFAD_INCLUDES . \PFAD_SHOPMD5 . $check->getVersionString() . '.csv';
            $struct  = $check->validateCsvFile($md5file, $files, $stats) === FileCheck::OK
                ? $stats
                : 1;
            $this->cache->set(self::CACHE_ID_MODIFIED_FILE_STRUCT, $struct, [\CACHING_GROUP_STATUS]);
        }
        $hash = \md5(($hash ?? 'validModifiedFileStruct') . '_' . $struct);

        return $struct === 0;
    }

    /**
     * checks the shop-filesystem-structure against 'admin/includes/shopmd5files/deleted_files_[shop-version].csv'
     *
     * @param string|null $hash
     * @return bool  true='no errors', false='something is wrong'
     */
    public function validOrphanedFilesStruct(?string &$hash = null): bool
    {
        if (($struct = $this->cache->get(self::CACHE_ID_ORPHANED_FILE_STRUCT)) === false) {
            $check   = new FileCheck();
            $files   = [];
            $stats   = 0;
            $csvFile = \PFAD_ROOT . \PFAD_ADMIN . \PFAD_INCLUDES . \PFAD_SHOPMD5
                . 'deleted_files_' . $check->getVersionString() . '.csv';
            $struct  = $check->validateCsvFile($csvFile, $files, $stats) === FileCheck::OK
                ? $stats
                : 1;
            $this->cache->set(self::CACHE_ID_ORPHANED_FILE_STRUCT, $struct, [\CACHING_GROUP_STATUS]);
        }
        $hash = \md5(($hash ?? 'validOrphanedFilesStruct') . '_' . $struct);

        return $struct === 0;
    }

    /**
     * @param string|null $hash
     * @return bool
     */
    public function validFolderPermissions(?string &$hash = null): bool
    {
        /** @var stdClass|false $struct */
        $struct = $this->cache->get(self::CACHE_ID_FOLDER_PERMISSIONS);
        if ($struct === false) {
            $filesystem = new Filesystem(\PFAD_ROOT);
            $filesystem->getFoldersChecked();
            $struct = $filesystem->getFolderStats();
            $this->cache->set(self::CACHE_ID_FOLDER_PERMISSIONS, $struct, [\CACHING_GROUP_STATUS]);
        }
        $hash = \md5(($hash ?? 'validFolderPermissions') . '_' . $struct->nCountInValid);

        return $struct->nCountInValid === 0;
    }

    /**
     * @return array<int, array<string, stdClass>>
     */
    public function getPluginSharedHooks(): array
    {
        $sharedPlugins = [];
        $sharedHookIds = $this->db->getObjects(
            'SELECT nHook
                FROM tpluginhook
                GROUP BY nHook
                HAVING COUNT(DISTINCT kPlugin) > 1'
        );
        foreach ($sharedHookIds as $hookData) {
            $hookID                 = (int)$hookData->nHook;
            $sharedPlugins[$hookID] = [];
            $plugins                = $this->db->getObjects(
                'SELECT DISTINCT tpluginhook.kPlugin, tplugin.cName, tplugin.cPluginID
                    FROM tpluginhook
                    INNER JOIN tplugin
                        ON tpluginhook.kPlugin = tplugin.kPlugin
                    WHERE tpluginhook.nHook = :hook
                        AND tplugin.nStatus = :state',
                [
                    'hook'  => $hookID,
                    'state' => State::ACTIVATED
                ]
            );
            foreach ($plugins as $plugin) {
                $sharedPlugins[$hookID][(string)$plugin->cPluginID] = $plugin;
            }
        }

        return $sharedPlugins;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function hasPendingUpdates(): bool
    {
        return (new Updater($this->db))->hasPendingUpdates();
    }

    /**
     * @return bool
     */
    public function hasActiveProfiler(): bool
    {
        return Profiler::getIsActive() !== 0;
    }

    /**
     * @return bool
     */
    public function hasInstallDir(): bool
    {
        return \is_dir(\PFAD_ROOT . \PFAD_INSTALL);
    }

    /**
     * @return array{db: string, php: string, diff: int}
     */
    public function hasMysqlPhpTimeMismatch(): array
    {
        try {
            $dbTimeString = $this->db->getSingleObject('SELECT NOW() AS time')?->time ?? 'now()';
            $dbTime       = new DateTime($dbTimeString);
            $phpTime      = new DateTime();

            return [
                'db'   => $dbTime->format('Y-m-d H:i:s'),
                'php'  => $phpTime->format('Y-m-d H:i:s'),
                'diff' => \abs($dbTime->getTimestamp() - $phpTime->getTimestamp())
            ];
        } catch (Exception) {
            return [
                'db'   => '0',
                'php'  => '0',
                'diff' => 0
            ];
        }
    }

    /**
     * @return bool
     */
    public function hasDifferentTemplateVersion(): bool
    {
        try {
            $template = Shop::Container()->getTemplateService()->getActiveTemplate();
        } catch (Exception) {
            return false;
        }
        return $template->getVersion() !== \APPLICATION_VERSION;
    }

    /**
     * @return bool
     */
    public function hasMobileTemplateIssue(): bool
    {
        try {
            $template = Shop::Container()->getTemplateService()->getActiveTemplate();
        } catch (Exception) {
            return false;
        }
        if ($template->isResponsive()) {
            $mobileTpl = $this->db->select('ttemplate', 'eTyp', 'mobil');
            if ($mobileTpl !== null) {
                $xmlFile = \PFAD_ROOT . \PFAD_TEMPLATES . $mobileTpl->cTemplate . '/' . \TEMPLATE_XML;
                if (\file_exists($xmlFile)) {
                    return true;
                }
                $this->db->delete('ttemplate', 'eTyp', 'mobil');
            }
        }

        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function hasStandardTemplateIssue(): bool
    {
        return $this->db->select('ttemplate', 'eTyp', 'standard') === null
            || Shop::Container()->getTemplateService()->getActiveTemplate()->getTemplate() === null;
    }

    /**
     * @return bool
     */
    public function hasValidEnvironment(): bool
    {
        PDOConnection::getInstance()->setConnection($this->db->getPDO());
        $systemcheck = new Environment();
        $systemcheck->executeTestGroup('Shop5');

        return $systemcheck->getIsPassed();
    }

    /**
     * @return bool
     */
    public function hasInstalledStandardLang(): bool
    {
        $defaultID = LanguageHelper::getDefaultLanguage()->getId();

        return some(
            LanguageHelper::getInstance()->getInstalled(),
            static function (LanguageModel $lang) use ($defaultID): bool {
                return $lang->getId() === $defaultID;
            }
        );
    }

    /**
     * @return array<string, array<int, AbstractTest>>
     */
    public function getEnvironmentTests(): array
    {
        PDOConnection::getInstance()->setConnection($this->db->getPDO());

        return (new Environment())->executeTestGroup('Shop5');
    }

    /**
     * @return Hosting
     */
    public function getPlatform(): Hosting
    {
        return new Hosting();
    }

    /**
     * @return array<int, array{key: string, value: string}>
     */
    public function getMySQLStats(): array
    {
        $lines = \array_map(static function (string $v): array {
            [$key, $value] = \explode(':', $v, 2);

            return ['key' => \trim($key), 'value' => \trim($value)];
        }, \explode('  ', $this->db->getServerStats()));

        return \array_merge([['key' => 'Version', 'value' => $this->db->getServerInfo()]], $lines);
    }

    /**
     * @return stdClass[]
     */
    public function getPaymentMethodsWithError(): array
    {
        $incorrectPaymentMethods = [];
        $paymentMethods          = $this->db->selectAll(
            'tzahlungsart',
            'nActive',
            1,
            '*',
            'cAnbieter, cName, nSort, kZahlungsart'
        );
        foreach ($paymentMethods as $method) {
            if (($logCount = ZahlungsLog::count($method->cModulId, \JTLLOG_LEVEL_ERROR)) > 0) {
                $method->logCount          = $logCount;
                $incorrectPaymentMethods[] = $method;
            }
        }

        return $incorrectPaymentMethods;
    }

    /**
     * @return stdClass[]
     */
    public function getOrphanedCategories(): array
    {
        return $this->db->getObjects(
            'SELECT kKategorie, cName
                FROM tkategorie
                WHERE kOberkategorie > 0
                    AND kOberkategorie NOT IN (SELECT DISTINCT kKategorie FROM tkategorie)'
        );
    }

    public function hasOrphanedCategories(): bool
    {
        return \count($this->getOrphanedCategories()) !== 0;
    }

    /**
     * @return bool
     */
    public function hasFullTextIndexError(): bool
    {
        return Settings::stringValue(Overview::SEARCH_FULLTEXT) !== 'N'
            && (!$this->db->getSingleObject(
                "SHOW INDEX
                    FROM tartikel
                    WHERE KEY_NAME = 'idx_tartikel_fulltext'"
            )
                || !$this->db->getSingleObject(
                    "SHOW INDEX
                    FROM tartikelsprache
                    WHERE KEY_NAME = 'idx_tartikelsprache_fulltext'"
                ));
    }

    /**
     * @param string|null $hash
     * @return bool
     */
    public function hasLicenseExpirations(?string &$hash = null): bool
    {
        $mapper       = new Mapper(new Manager($this->db, $this->cache));
        $toBeExpired  = $mapper->getCollection()->getAboutToBeExpired()->count();
        $boundExpired = $mapper->getCollection()->getBoundExpired()->count();
        $hash         = \md5(($hash ?? 'hasLicenseExpirations') . '_' . $toBeExpired . '_' . $boundExpired);

        return $toBeExpired > 0 || $boundExpired > 0;
    }

    /**
     * @return bool
     */
    public function hasNewPluginVersions(): bool
    {
        if (\SAFE_MODE === true) {
            return false;
        }
        $data = $this->db->getObjects(
            'SELECT `kPlugin`, `nVersion`, `bExtension`
                FROM `tplugin`'
        );
        if (\count($data) === 0) {
            return false; // there are no plugins installed
        }
        foreach ($data as $item) {
            try {
                $plugin = Helper::getLoader((int)$item->bExtension === 1)->init((int)$item->kPlugin);
            } catch (Exception) {
                continue;
            }
            if ($plugin->getCurrentVersion()->greaterThan($item->nVersion)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool $has
     * @return bool|Collection
     */
    public function getLocalizationProblems(bool $has = true): bool|Collection
    {
        if (\SAFE_MODE === true) {
            return false;
        }
        $languages = \collect(LanguageHelper::getAllLanguages(0, true, true));
        $factory   = new LocalizationCheckFactory($this->db, $languages);
        $results   = new Collection();
        foreach ($factory->getAllChecks() as $check) {
            $result  = new Result();
            $excess  = $check->getExcessLocalizations();
            $missing = $check->getItemsWithoutLocalization();
            $result->setLocation($check->getLocation());
            $result->setClassName(\get_class($check));
            $result->setExcessLocalizations($excess);
            $result->setMissingLocalizations($missing);
            if ($has === true && ($missing->count() > 0 || $excess->count() > 0)) {
                return true;
            }
            $results->push($result);
        }

        return $has ? false : $results;
    }

    /**
     * Checks, whether the password reset mail template contains the old variable $neues_passwort.
     *
     * @return bool
     */
    public function hasInvalidPasswordResetMailTemplate(): bool
    {
        $translations = $this->db->getObjects(
            "SELECT lang.cContentText, lang.cContentHtml
                FROM temailvorlagesprache lang
                JOIN temailvorlage
                ON lang.kEmailvorlage = temailvorlage.kEmailvorlage
                WHERE temailvorlage.cName = 'Passwort vergessen'"
        );
        foreach ($translations as $t) {
            $old = '{$neues_passwort}';
            if (\str_contains($t->cContentHtml, $old) || \str_contains($t->cContentText, $old)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether SMTP is configured for sending mails but no encryption method is chosen for email server
     * communication
     *
     * @param string|null $hash
     * @return bool
     */
    public function hasInsecureMailConfig(?string &$hash = null): bool
    {
        $conf = Shop::getSettingSection(\CONF_EMAILS);
        $hash = \md5(($hash ?? 'hasInsecureMailConfig') . '_' . $conf['email_methode']);

        return $conf['email_methode'] === 'smtp' && empty(\trim($conf['email_smtp_verschluesselung']));
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function needPasswordRehash2FA(): bool
    {
        $passwordService = Shop::Container()->getPasswordService();
        $hashes          = $this->db->getObjects(
            'SELECT *
                FROM tadmin2facodes
                GROUP BY kAdminlogin'
        );

        return some($hashes, static function (stdClass $hash) use ($passwordService): bool {
            return $passwordService->needsRehash($hash->cEmergencyCode);
        });
    }

    /**
     * @return stdClass[]
     */
    public function getDuplicateLinkGroupTemplateNames(): array
    {
        return $this->db->getObjects(
            'SELECT * FROM tlinkgruppe
                GROUP BY cTemplatename
                HAVING COUNT(*) > 1'
        );
    }

    /**
     * @param int         $type
     * @param string|null $hash
     * @return int
     */
    public function getExportFormatErrorCount(int $type = Validator::SYNTAX_FAIL, ?string &$hash = null): int
    {
        $cacheKey = self::CACHE_ID_EXPORT_SYNTAX_CHECK . $type;
        /** @var int|false $syntaxErrCnt */
        $syntaxErrCnt = $this->cache->get($cacheKey);
        if ($syntaxErrCnt === false) {
            $syntaxErrCnt = $this->db->getSingleInt(
                'SELECT COUNT(*) AS cnt FROM texportformat WHERE nFehlerhaft = :type',
                'cnt',
                ['type' => $type]
            );
            $this->cache->set($cacheKey, $syntaxErrCnt, [\CACHING_GROUP_STATUS, self::CACHE_ID_EXPORT_SYNTAX_CHECK]);
        }
        $hash = \md5($hash . $syntaxErrCnt);

        return $syntaxErrCnt;
    }

    /**
     * @param int         $type
     * @param string|null $hash
     * @return int
     */
    public function getEmailTemplateSyntaxErrorCount(int $type = MailTplModel::SYNTAX_FAIL, ?string &$hash = null): int
    {
        $cacheKey = self::CACHE_ID_EMAIL_SYNTAX_CHECK . $type;
        /** @var int|false $syntaxErrCnt */
        $syntaxErrCnt = $this->cache->get($cacheKey);
        if ($syntaxErrCnt === false) {
            $syntaxErrCnt = 0;
            $templates    = $this->db->getObjects(
                'SELECT cModulId, kPlugin FROM temailvorlage WHERE nFehlerhaft = :type',
                ['type' => $type]
            );
            $factory      = new TemplateFactory($this->db);
            foreach ($templates as $template) {
                $module = $template->cModulId;
                if ($template->kPlugin > 0) {
                    $module = 'kPlugin_' . $template->kPlugin . '_' . $template->cModulId;
                }
                $syntaxErrCnt += $factory->getTemplate($module) !== null ? 1 : 0;
            }

            $this->cache->set($cacheKey, $syntaxErrCnt, [\CACHING_GROUP_STATUS, self::CACHE_ID_EMAIL_SYNTAX_CHECK]);
        }
        $hash = \md5($hash . $syntaxErrCnt);

        return $syntaxErrCnt;
    }

    /**
     * @return bool
     */
    public function hasExtensionSOAP(): bool
    {
        return \extension_loaded('soap');
    }

    /**
     * @return stdClass[]
     */
    public function getExtensions(): array
    {
        $nice       = Nice::getInstance($this->db, $this->cache);
        $extensions = $nice->gibAlleMoeglichenModule();
        foreach ($extensions as $extension) {
            $extension->bActive = $nice->checkErweiterung($extension->kModulId);
        }

        return $extensions;
    }
}
