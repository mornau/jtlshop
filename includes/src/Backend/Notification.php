<?php

declare(strict_types=1);

namespace JTL\Backend;

use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use JTL\DB\DbInterface;
use JTL\Events\Dispatcher;
use JTL\Export\Validator;
use JTL\IO\IOResponse;
use JTL\Language\LanguageHelper;
use JTL\Link\Admin\LinkAdmin;
use JTL\Mail\Template\Model;
use JTL\Router\Route;
use JTL\Shop;
use JTL\Smarty\ContextType;
use JTL\Smarty\JTLSmarty;

use function Functional\pluck;

/**
 * Class Notification
 * @package JTL\Backend
 */
class Notification implements IteratorAggregate, Countable
{
    /**
     * @var NotificationEntry[]
     */
    private array $array = [];

    /**
     * @var Notification|null
     */
    private static ?Notification $instance = null;

    /**
     * Notification constructor.
     * @param DbInterface $db
     */
    public function __construct(private readonly DbInterface $db)
    {
        self::$instance = $this;
    }

    /**
     * @param DbInterface|null $db
     * @return self
     */
    public static function getInstance(DbInterface $db = null): self
    {
        return self::$instance ?? new self($db ?? Shop::Container()->getDB());
    }

    /**
     * @param int         $type
     * @param string      $title
     * @param string|null $description
     * @param string|null $url
     * @param string|null $hash
     */
    public function add(
        int $type,
        string $title,
        ?string $description = null,
        ?string $url = null,
        ?string $hash = null
    ): void {
        $this->addNotify(new NotificationEntry($type, $title, $description, $url, $hash));
    }

    /**
     * @param NotificationEntry $notify
     */
    public function addNotify(NotificationEntry $notify): void
    {
        $this->array[] = $notify;
    }

    /**
     * @param bool $withIgnored
     * @return int - highest type in record
     */
    public function getHighestType(bool $withIgnored = false): int
    {
        $type = NotificationEntry::TYPE_NONE;
        foreach ($this as $notify) {
            /** @var NotificationEntry $notify */
            if (($withIgnored || !$notify->isIgnored()) && $notify->getType() > $type) {
                $type = $notify->getType();
            }
        }

        return $type;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count(
            \array_filter($this->array, static function (NotificationEntry $item): bool {
                return !$item->isIgnored();
            })
        );
    }

    /**
     * @return int
     */
    public function totalCount(): int
    {
        return \count($this->array);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        \usort($this->array, static function (NotificationEntry $a, NotificationEntry $b): int {
            return $b->getType() <=> $a->getType();
        });

        return new ArrayIterator($this->array);
    }

    /**
     * Build default system notifications.
     *
     * @param bool $flushCache
     * @return $this
     * @throws Exception
     * @todo Remove translated messages
     */
    public function buildDefault(bool $flushCache = false): self
    {
        $adminURL  = Shop::getAdminURL() . '/';
        $cache     = Shop::Container()->getCache();
        $status    = Status::getInstance($this->db, $cache, $flushCache);
        $linkAdmin = new LinkAdmin($this->db, $cache);

        Shop::Container()->getGetText()->loadAdminLocale('notifications');

        if ($status->hasPendingUpdates()) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('hasPendingUpdatesTitle'),
                \__('hasPendingUpdatesMessage'),
                $adminURL . Route::DBUPDATER
            );
            return $this;
        }

        $hash = 'validFolderPermissions';
        if (!$status->validFolderPermissions($hash)) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('validFolderPermissionsTitle'),
                \__('validFolderPermissionsMessage'),
                $adminURL . Route::PERMISSIONCHECK,
                $hash
            );
        }
        $times = $status->hasMysqlPhpTimeMismatch();
        if ($times['diff'] > 1) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('mysqlTimeErrorTitle'),
                \sprintf(\__('mysqlTimeErrorMessage'), $times['db'], $times['php'])
            );
        }

        if ($status->hasInstallDir()) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('hasInstallDirTitle'),
                \__('hasInstallDirMessage')
            );
        }

        if (!$status->validDatabaseStruct()) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('validDatabaseStructTitle'),
                \__('validDatabaseStructMessage'),
                $adminURL . Route::DBCHECK
            );
        }

        $hash = 'validModifiedFileStruct';
        if (!$status->validModifiedFileStruct($hash) || !$status->validOrphanedFilesStruct($hash)) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('validModifiedFileStructTitle'),
                \__('validModifiedFileStructMessage'),
                $adminURL . Route::FILECHECK,
                $hash
            );
        }

        if ($status->hasMobileTemplateIssue()) {
            $this->add(
                NotificationEntry::TYPE_INFO,
                \__('hasMobileTemplateIssueTitle'),
                \__('hasMobileTemplateIssueMessage'),
                $adminURL . Route::TEMPLATE
            );
        }

        if ($status->hasStandardTemplateIssue()) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('hasStandardTemplateIssueTitle'),
                \__('hasStandardTemplateIssueMessage'),
                $adminURL . Route::TEMPLATE
            );
        }

        if ($status->hasActiveProfiler()) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('hasActiveProfilerTitle'),
                \__('hasActiveProfilerMessage')
            );
        }

        if ($status->hasNewPluginVersions()) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('hasNewPluginVersionsTitle'),
                \__('hasNewPluginVersionsMessage'),
                $adminURL . Route::PLUGIN_MANAGER
            );
        }

        $hash = 'hasLicenseExpirations';
        if ($status->hasLicenseExpirations($hash)) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('hasLicenseExpirationsTitle'),
                \__('hasLicenseExpirationsMessage'),
                $adminURL . Route::LICENSE,
                $hash
            );
        }

        /* REMOTE CALL
        if (($subscription =  Shop()->RS()->getSubscription()) !== null) {
            if ((int)$subscription->bUpdate === 1) {
                if ((int)$subscription->nDayDiff <= 0) {
                    $this->add(
                        NotificationEntry::TYPE_WARNING,
                        'Subscription',
                        'Ihre Subscription ist abgelaufen. Jetzt erneuern.',
                        'https://jtl-url.de/subscription'
                    );
                } else {
                    $this->add(
                        NotificationEntry::TYPE_INFO,
                        'Subscription',
                        "Ihre Subscription läuft in {$subscription->nDayDiff} Tagen ab.",
                        'https://jtl-url.de/subscription'
                    );
                }
            }
        }
        */

        if ($status->hasFullTextIndexError()) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('hasFullTextIndexErrorTitle'),
                \__('hasFullTextIndexErrorMessage'),
                $adminURL . Route::SEARCHCONFIG
            );
        }

        if ($status->hasInvalidPasswordResetMailTemplate()) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('hasInvalidPasswordResetMailTemplateTitle'),
                \__('hasInvalidPasswordResetMailTemplateMessage'),
                $adminURL . Route::EMAILTEMPLATES
            );
        }

        $hash = 'hasInsecureMailConfig';
        if ($status->hasInsecureMailConfig($hash)) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('hasInsecureMailConfigTitle'),
                \__('hasInsecureMailConfigMessage'),
                $adminURL . Route::CONFIG . '?kSektion=3',
                $hash
            );
        }

        try {
            if ($status->needPasswordRehash2FA()) {
                $this->add(
                    NotificationEntry::TYPE_DANGER,
                    \__('needPasswordRehash2FATryTitle'),
                    \__('needPasswordRehash2FATryMessage'),
                    $adminURL . Route::USERS
                );
            }
        } catch (Exception) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('needPasswordRehash2FACatchTitle'),
                \__('needPasswordRehash2FACatchMessage'),
                $adminURL . Route::DBUPDATER
            );
        }

        if (\count($status->getDuplicateLinkGroupTemplateNames()) > 0) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('getDuplicateLinkGroupTemplateNamesTitle'),
                \sprintf(
                    \__('getDuplicateLinkGroupTemplateNamesMessage'),
                    \implode(', ', pluck($status->getDuplicateLinkGroupTemplateNames(), 'cName'))
                ),
                $adminURL . Route::LINKS
            );
        }

        if ($linkAdmin->getDuplicateSpecialLinks()->count() > 0) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('duplicateSpecialLinkTitle'),
                \__('duplicateSpecialLinkDesc'),
                $adminURL . Route::LINKS
            );
        }

        if (($missingTranslations = $linkAdmin->getUntranslatedPageIDs()->count()) > 0) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('Missing translations'),
                \sprintf(\__('%d pages are not translated in all available languages.'), $missingTranslations),
                $adminURL . Route::LINKS
            );
        }

        if (($missingSystemPages = $linkAdmin->getMissingSystemPages()->count()) > 0) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('Missing special pages'),
                \sprintf(\__('%d special pages are missing.'), $missingSystemPages),
                $adminURL . Route::LINKS
            );
        }

        if (($expSyntaxErrorCount = $status->getExportFormatErrorCount()) > 0) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('getExportFormatErrorCountTitle'),
                \sprintf(\__('getExportFormatErrorCountMessage'), $expSyntaxErrorCount),
                $adminURL . Route::EXPORT
            );
        }

        $hash = 'hasUncheckedExportTemplates';
        if (($expSyntaxErrorCount = $status->getExportFormatErrorCount(Validator::SYNTAX_NOT_CHECKED, $hash)) > 0) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('getExportFormatUncheckedCountTitle'),
                \sprintf(\__('getExportFormatUncheckedCountMessage'), $expSyntaxErrorCount),
                $adminURL . Route::EXPORT,
                $hash
            );
        }

        if (($emailSyntaxErrCount = $status->getEmailTemplateSyntaxErrorCount()) > 0) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('getEmailTemplateSyntaxErrorCountTitle'),
                \sprintf(\__('getEmailTemplateSyntaxErrorCountMessage'), $emailSyntaxErrCount),
                $adminURL . Route::EMAILTEMPLATES
            );
        }

        $hash = 'hasUncheckedEmailTemplates';
        if (($emailSyntaxErrCount = $status->getEmailTemplateSyntaxErrorCount(Model::SYNTAX_NOT_CHECKED, $hash)) > 0) {
            $this->add(
                NotificationEntry::TYPE_WARNING,
                \__('getEmailTemplateSyntaxUncheckedCountTitle'),
                \sprintf(\__('getEmailTemplateSyntaxUncheckedCountMessage'), $emailSyntaxErrCount),
                $adminURL . Route::EMAILTEMPLATES,
                $hash
            );
        }

        if (!$status->hasExtensionSOAP()) {
            $this->add(
                NotificationEntry::TYPE_INFO,
                \__('ustIdMiasCheckTitle'),
                \__('ustIdMiasCheckMessage'),
                $adminURL . Route::CONFIG . '?kSektion=6'
            );
        }

        if (!$status->hasInstalledStandardLang()) {
            $this->add(
                NotificationEntry::TYPE_DANGER,
                \__('defaultLangNotInstalledTitle'),
                \sprintf(
                    \__('defaultLangNotInstalledMessage'),
                    LanguageHelper::getDefaultLanguage()->getNameDE()
                )
            );
        }

        return $this;
    }

    /**
     * @param IOResponse $response
     * @param string     $hash
     * @return void
     * @throws Exception
     */
    protected function ignoreNotification(IOResponse $response, string $hash): void
    {
        $this->db->upsert(
            'tnotificationsignore',
            (object)[
                'user_id'           => Shop::Container()->getAdminAccount()->getID(),
                'notification_hash' => $hash,
                'created'           => 'NOW()',
            ],
            ['created']
        );

        $response->assignDom($hash, 'outerHTML', '');
    }

    /**
     * @param IOResponse $response
     * @return void
     * @throws Exception
     */
    protected function resetIgnoredNotifications(IOResponse $response): void
    {
        $this->db->delete(
            'tnotificationsignore',
            'user_id',
            Shop::Container()->getAdminAccount()->getID()
        );

        $this->updateNotifications($response, true);
    }

    /**
     * @param IOResponse $response
     * @param bool       $flushCache
     * @return void
     * @throws Exception
     */
    protected function updateNotifications(IOResponse $response, bool $flushCache = false): void
    {
        Dispatcher::getInstance()->fire('backend.notification', $this->buildDefault($flushCache));
        $res    = $this->db->getCollection(
            'SELECT notification_hash
                FROM tnotificationsignore
                WHERE user_id = :userID', // AND NOW() < DATE_ADD(created, INTERVAL 7 DAY)',
            ['userID' => Shop::Container()->getAdminAccount()->getID()]
        );
        $hashes = $res->keyBy('notification_hash');
        foreach ($this->array as $notificationEntry) {
            if (($hash = $notificationEntry->getHash()) !== null && $hashes->has($hash)) {
                $notificationEntry->setIgnored(true);
                $hashes->forget($hash);
            }
        }
        if ($hashes->count() > 0) {
            $this->db->query(
                "DELETE FROM tnotificationsignore
                    WHERE notification_hash IN ('" . $hashes->implode('notification_hash', "', '") . "')"
            );
        }

        $response->assignDom('notify-drop', 'innerHTML', self::getNotifyDropIO()['tpl']);
    }

    /**
     * @param string     $action
     * @param mixed|null $data
     * @return IOResponse
     * @throws Exception
     */
    public static function ioNotification(string $action, $data = null): IOResponse
    {
        $response      = new IOResponse();
        $notifications = self::getInstance();

        switch ($action) {
            case 'update':
                $notifications->updateNotifications($response);
                break;
            case 'refresh':
                $notifications->updateNotifications($response, true);
                break;
            case 'dismiss':
                $notifications->ignoreNotification($response, (string)$data);
                break;
            case 'reset':
                $notifications->resetIgnoredNotifications($response);
                break;
            default:
                break;
        }

        return $response;
    }

    /**
     * @return array{tpl: string, type: string}
     * @former getNotifyDropIO()
     * @since 5.2.0
     */
    public static function getNotifyDropIO(): array
    {
        return [
            'tpl'  => JTLSmarty::getInstance(false, ContextType::BACKEND)
                ->assign('notifications', self::getInstance())
                ->fetch('tpl_inc/notify_drop.tpl'),
            'type' => 'notify'
        ];
    }
}
