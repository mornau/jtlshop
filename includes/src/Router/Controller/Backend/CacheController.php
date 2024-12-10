<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use Exception;
use JTL\Backend\DirManager;
use JTL\Backend\Permissions;
use JTL\Backend\Settings\Manager;
use JTL\Backend\Settings\SectionFactory;
use JTL\Backend\Settings\Sections\SectionInterface;
use JTL\Helpers\Form;
use JTL\Helpers\GeneralObject;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\Minify\MinifyService;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Class CacheController
 * @package JTL\Router\Controller\Backend
 */
class CacheController extends AbstractBackendController
{
    /**
     * @var string
     */
    private string $tab = 'uebersicht';

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::OBJECTCACHE_VIEW);
        $this->getText->loadAdminLocale('pages/cache');

        $this->tab      = Request::postVar('tab', 'uebersicht');
        $action         = Form::validateToken() ? Request::postVar('a') : null;
        $cacheAction    = Request::pString('cache-action');
        $settingManager = new Manager($this->db, $smarty, $this->account, $this->getText, $this->alertService);
        $postData       = Text::filterXSS($_POST);
        $sectionFactory = new SectionFactory();
        $cacheSection   = $sectionFactory->getSection(\CONF_CACHING, $settingManager);
        $this->getText->loadConfigLocales();
        if (0 < \mb_strlen(Request::verifyGPDataString('tab'))) {
            $smarty->assign('tab', Request::verifyGPDataString('tab'));
        }
        try {
            $this->cache->setJtlCacheConfig(
                $this->db->selectAll('teinstellungen', 'kEinstellungenSektion', \CONF_CACHING)
            );
        } catch (Exception $exc) {
            $this->alertService->addError(\__('exception') . ': ' . $exc->getMessage(), 'errorException');
        }
        $this->checkAction($action, $cacheAction, $postData, $settingManager, $cacheSection);
        $options = $this->cache->getOptions();
        $smarty->assign('method', \ucfirst($options['method']))
            ->assign('all_methods', $this->cache->getAllMethods())
            ->assign('stats', $this->cache->getStats());

        $this->assignSettings($sectionFactory, $settingManager);
        $this->assignMethods();
        if (!empty($this->cache->getError())) {
            $this->alertService->addError($this->cache->getError(), 'errorCache');
        }

        return $smarty->assign('caching_groups', $this->getGroups())
            ->assign('cache_enabled', isset($options['activated']) && $options['activated'] === true)
            ->assign('options', $options)
            ->assign('opcache_stats', $this->getOpcacheStats())
            ->assign('tplcacheStats', $this->getTemplateCacheStats())
            ->assign('disabled_caches', $this->getDisabledTags())
            ->assign('step', 'uebersicht')
            ->assign('tab', $this->tab)
            ->assign('route', $this->route)
            ->getResponse('cache.tpl');
    }

    private function assignSettings(SectionFactory $sectionFactory, Manager $settingManager): void
    {
        $cacheSection = $sectionFactory->getSection(\CONF_CACHING, $settingManager);
        $cacheSection->load();
        $advancedSettings = [];
        $settings         = [];
        foreach ($cacheSection->getSubsections() as $subsection) {
            foreach ($subsection->getItems() as $item) {
                if ($item->getShowDefault() === 0 || $item->getShowDefault() === 2) {
                    $advancedSettings[] = $item;
                } elseif ($item->getShowDefault() === 1) {
                    $settings[] = $item;
                }
            }
        }
        $this->getSmarty()->assign('advanced_settings', $advancedSettings)
            ->assign('settings', $settings);
    }

    /**
     * @return array{name: string, nicename: string, value: string, description: string, key_count: int}[]
     */
    private function getGroups(): array
    {
        $result = [];
        foreach ($this->cache->getCachingGroups() as $cachingGroup) {
            $cachingGroup['key_count'] = \count($this->cache->getKeysByTag([\constant($cachingGroup['name'])]));

            $result[] = $cachingGroup;
        }

        return $result;
    }

    /**
     * @return void
     * @throws \JsonException
     */
    private function assignMethods(): void
    {
        $availableMethods     = [];
        $disFunctionalMethods = [];
        $nonAvailableMethods  = [];
        foreach ($this->cache->checkAvailability() as $name => $state) {
            if ($name === 'null') {
                continue;
            }
            if ($state['functional'] === true) {
                $availableMethods[] = $name;
            } elseif ($state['available'] === true) {
                $disFunctionalMethods[] = $name;
            } else {
                $nonAvailableMethods[] = $name;
            }
        }
        $this->getSmarty()->assign('functional_methods', \json_encode($availableMethods, \JSON_THROW_ON_ERROR))
            ->assign('disfunctional_methods', \json_encode($disFunctionalMethods, \JSON_THROW_ON_ERROR))
            ->assign('non_available_methods', \json_encode($nonAvailableMethods, \JSON_THROW_ON_ERROR));
    }

    /**
     * @return string[]
     */
    private function getDisabledTags(): array
    {
        $deactivated       = $this->db->select(
            'teinstellungen',
            ['kEinstellungenSektion', 'cName'],
            [\CONF_CACHING, 'caching_types_disabled']
        );
        $currentlyDisabled = [];
        if ($deactivated !== null && isset($deactivated->cWert)) {
            /** @var string[] $currentlyDisabled */
            $currentlyDisabled = ($deactivated->cWert !== '')
                ? \unserialize($deactivated->cWert, ['allowed_classes' => false])
                : [];
        }

        return $currentlyDisabled;
    }

    /**
     * @param string $cacheAction
     * @param array  $postData
     * @return void
     */
    private function actionMassAction(string $cacheAction, array $postData): void
    {
        switch ($cacheAction) {
            case 'flush':
                if (!GeneralObject::isCountable('cache-types', $postData)) {
                    $this->alertService->addError(\__('errorNoCacheType'), 'errorNoCacheType');
                    break;
                }
                $this->flushCache($postData['cache-types']);
                break;
            case 'activate':
                if (!\is_array(Request::postVar('cache-types'))) {
                    $this->alertService->addError(\__('errorNoCacheType'), 'errorNoCacheType');
                    break;
                }
                $this->activateGroup($postData['cache-types']);
                break;
            case 'deactivate':
                if (!GeneralObject::isCountable('cache-types', $postData)) {
                    $this->alertService->addError(\__('errorNoCacheType'), 'errorNoCacheType');
                    break;
                }
                $this->deactivateGroup($postData['cache-types']);
                break;
            default:
                break;
        }
    }

    /**
     * @param SectionInterface $cacheSection
     * @return void
     */
    private function actionSettings(SectionInterface $cacheSection): void
    {
        $availableMethods = $this->getAvailableMethods();
        foreach ($cacheSection->update($_POST) as $item) {
            if ($item['id'] !== 'caching_method' || $item['value'] !== 'auto') {
                continue;
            }
            $value        = new stdClass();
            $value->cWert = 'null';
            if (\count($availableMethods) > 0) {
                if (\in_array('redis', $availableMethods, true)) {
                    $value->cWert = 'redis';
                } elseif (\in_array('sqlite', $availableMethods, true)) {
                    $value->cWert = 'sqlite';
                } elseif (\in_array('memcache', $availableMethods, true)) {
                    $value->cWert = 'memcache';
                } elseif (\in_array('memcached', $availableMethods, true)) {
                    $value->cWert = 'memcached';
                } elseif (\in_array('apc', $availableMethods, true)) {
                    $value->cWert = 'apc';
                } elseif (\in_array('advancedfile', $availableMethods, true)) {
                    $value->cWert = 'advancedfile';
                } elseif (\in_array('file', $availableMethods, true)) {
                    $value->cWert = 'file';
                }
            }
            $this->db->update(
                'teinstellungen',
                ['kEinstellungenSektion', 'cName'],
                [\CONF_CACHING, 'caching_method'],
                $value
            );
            if ($value->cWert !== 'null') {
                $this->alertService->addSuccess(
                    '<strong>' . $value->cWert . '</strong>' . \__('successCacheMethodSave'),
                    'successCacheDelete'
                );
            } else {
                $this->alertService->addError(\__('errorCacheMethodSelect'), 'errorCacheMethodSelect');
            }
        }
        $this->cache->flushAll();
        $this->cache->setJtlCacheConfig($this->db->selectAll('teinstellungen', 'kEinstellungenSektion', \CONF_CACHING));
        $this->alertService->addSuccess(\__('successConfigSave'), 'successConfigSave');
        $this->cache->flushTags([\CACHING_GROUP_OPTION]);
        $this->tab = 'settings';
    }

    /**
     * @return void
     * @throws Exception
     */
    private function actionFlushTemplateCache(): void
    {
        $notice       = '';
        $error        = '';
        $callback     = static function (array $params) {
            if (\str_starts_with($params['filename'], '.')) {
                return;
            }
            if (!$params['isdir']) {
                if (@\unlink($params['path'] . $params['filename'])) {
                    $params['count']++;
                } else {
                    $params['error'] .= \sprintf(
                        \__('errorFileDelete'),
                        '<strong>' . $params['path'] . $params['filename'] . '</strong>'
                    );
                    $params['error'] .= '<br/>';
                }
            } elseif (!@\rmdir($params['path'] . $params['filename'])) {
                $params['error'] .= \sprintf(
                    \__('errorDirDelete'),
                    '<strong>' . $params['path'] . $params['filename'] . '</strong>'
                );
                $params['error'] .= '<br/>';
            }
        };
        $deleteCount  = 0;
        $cbParameters = [
            'count'  => &$deleteCount,
            'notice' => &$notice,
            'error'  => &$error
        ];
        $template     = Shop::Container()->getTemplateService()->getActiveTemplate();
        $dirMan       = new DirManager();
        $dirMan->getData(\PFAD_ROOT . \PFAD_COMPILEDIR . $template->getDir(), $callback, $cbParameters);
        $dirMan->getData(\PFAD_ROOT . \PFAD_ADMIN . \PFAD_COMPILEDIR, $callback, $cbParameters);
        $ms = new MinifyService();
        $ms->flushCache();
        $this->alertService->addError($error, 'errorCache');
        $this->alertService->addNotice($notice, 'noticeCache');
        $this->alertService->addSuccess(
            \sprintf(
                \__('successTemplateCacheDelete'),
                \number_format($cbParameters['count'])
            ),
            'successTemplateCacheDelete'
        );
    }

    /**
     * @param array<string, string> $postData
     * @return void
     */
    private function actionBenchmark(array $postData): void
    {
        $this->tab = 'benchmark';
        $methods   = 'all';
        $repeat    = Request::pInt('repeat') ?: 1;
        $runCount  = Request::pInt('runcount') ?: 1000;
        switch ($postData['testdata'] ?? 'string') {
            case 'array':
                $testData = ['test1' => 'string number one', 'test2' => 'string number two', 'test3' => 333];
                break;
            case 'object':
                $testData        = new stdClass();
                $testData->test1 = 'string number one';
                $testData->test2 = 'string number two';
                $testData->test3 = 333;
                break;
            case 'string':
            default:
                $testData = 'simple short string';
                break;
        }
        if (\is_array(Request::postVar('methods'))) {
            $methods = $postData['methods'];
        }
        $benchResults = $this->cache->benchmark($methods, $testData, $runCount, $repeat, false, true);
        $this->getSmarty()->assign('bench_results', $benchResults);
    }

    /**
     * @return stdClass
     * @throws Exception
     */
    private function getTemplateCacheStats(): stdClass
    {
        $stats           = new stdClass();
        $stats->frontend = [];
        $stats->backend  = [];

        $callback = static function (array $pParameters): void {
            if (!$pParameters['isdir']) {
                $fileObj           = new stdClass();
                $fileObj->filename = $pParameters['filename'];
                $fileObj->path     = $pParameters['path'];
                $fileObj->fullname = $pParameters['path'] . $pParameters['filename'];

                $pParameters['files'][] = $fileObj;
            }
        };
        $template = Shop::Container()->getTemplateService()->getActiveTemplate();
        $dirMan   = new DirManager();
        $dirMan->getData(\PFAD_ROOT . \PFAD_COMPILEDIR . $template->getDir(), $callback, ['files' => &$stats->frontend])
            ->getData(\PFAD_ROOT . \PFAD_ADMIN . \PFAD_COMPILEDIR, $callback, ['files' => &$stats->backend]);

        return $stats;
    }

    /**
     * @return stdClass|null
     */
    private function getOpcacheStats(): ?stdClass
    {
        if (!\function_exists('opcache_get_status')) {
            return null;
        }
        $data                = \opcache_get_status();
        $stats               = new stdClass();
        $stats->enabled      = ($data['opcache_enabled'] ?? false) === true;
        $stats->memoryFree   = isset($data['memory_usage']['free_memory'])
            ? \round($data['memory_usage']['free_memory'] / 1024 / 1024, 2)
            : -1;
        $stats->memoryUsed   = isset($data['memory_usage']['used_memory'])
            ? \round($data['memory_usage']['used_memory'] / 1024 / 1024, 2)
            : -1;
        $stats->numberScrips = $data['opcache_statistics']['num_cached_scripts'] ?? -1;
        $stats->numberKeys   = $data['opcache_statistics']['num_cached_keys'] ?? -1;
        $stats->hits         = $data['opcache_statistics']['hits'] ?? -1;
        $stats->misses       = $data['opcache_statistics']['misses'] ?? -1;
        $stats->hitRate      = isset($data['opcache_statistics']['opcache_hit_rate'])
            ? \round($data['opcache_statistics']['opcache_hit_rate'], 2)
            : -1;
        $stats->scripts      = $data['scripts'] ?? [];

        return $stats;
    }

    /**
     * @param mixed            $action
     * @param string           $cacheAction
     * @param array            $postData
     * @param Manager          $settingManager
     * @param SectionInterface $cacheSection
     * @return void
     * @throws Exception
     */
    public function checkAction(
        mixed $action,
        string $cacheAction,
        array $postData,
        Manager $settingManager,
        SectionInterface $cacheSection
    ): void {
        switch ($action) {
            case 'cacheMassAction':
                //mass action cache flush
                $this->tab = 'massaction';
                $this->actionMassAction($cacheAction, $postData);
                break;
            case 'flush_object_cache':
                $this->tab = 'massaction';
                if ($this->cache->flushAll() !== false) {
                    $this->alertService->addSuccess(\__('successCacheDelete'), 'successCacheDelete');
                } else {
                    $this->alertService->addError(\__('errorCacheDelete'), 'errorCacheDelete');
                }
                break;
            case 'settings':
                if (Request::postVar('resetSetting') !== null) {
                    $settingManager->resetSetting(Request::pString('resetSetting'));
                    break;
                }
                $this->actionSettings($cacheSection);
                break;
            case 'benchmark':
                $this->actionBenchmark($postData);
                break;
            case 'flush_template_cache':
                $this->actionFlushTemplateCache();
                break;
            default:
                break;
        }
    }

    /**
     * @param string[] $cachetypes
     */
    public function flushCache(array $cachetypes): void
    {
        $okCount = 0;
        foreach ($cachetypes as $cacheType) {
            $hookInfo = ['type' => $cacheType, 'key' => null, 'isTag' => true];
            $flush    = $this->cache->flushTags([$cacheType], $hookInfo);
            if ($flush < 0) {
                $this->alertService->addError(
                    \sprintf(\__('errorCacheTypeDelete'), $cacheType),
                    'errorCacheTypeDelete'
                );
            } else {
                $okCount++;
            }
        }
        if ($okCount > 0) {
            $this->alertService->addSuccess($okCount . \__('successCacheEmptied'), 'successCacheEmptied');
        }
    }

    /**
     * @param string[] $cachetypes
     */
    public function activateGroup(array $cachetypes): void
    {
        $currentlyDisabled = $this->getDisabledTags();
        foreach ($cachetypes as $cacheType) {
            $index = \array_search($cacheType, $currentlyDisabled, true);
            if (\is_int($index)) {
                unset($currentlyDisabled[$index]);
            }
        }
        $upd        = new stdClass();
        $upd->cWert = \serialize($currentlyDisabled);
        $res        = $this->db->update(
            'teinstellungen',
            ['kEinstellungenSektion', 'cName'],
            [\CONF_CACHING, 'caching_types_disabled'],
            $upd
        );
        if ($res > 0) {
            $this->alertService->addSuccess(\__('successCacheTypeActivate'), 'successCacheTypeActivate');
        }
    }

    /**
     * @param string[] $cachetypes
     */
    public function deactivateGroup(array $cachetypes): void
    {
        $currentlyDisabled = $this->getDisabledTags();
        foreach ($cachetypes as $cacheType) {
            $this->cache->flushTags([$cacheType]);
            $currentlyDisabled[] = $cacheType;
        }
        $currentlyDisabled = \array_unique($currentlyDisabled);
        $upd               = new stdClass();
        $upd->cWert        = \serialize($currentlyDisabled);
        $res               = $this->db->update(
            'teinstellungen',
            ['kEinstellungenSektion', 'cName'],
            [\CONF_CACHING, 'caching_types_disabled'],
            $upd
        );
        if ($res > 0) {
            $this->alertService->addSuccess(
                \__('successCacheTypeDeactivate'),
                'successCacheTypeDeactivate'
            );
        }
    }

    /**
     * @return array
     */
    public function getAvailableMethods(): array
    {
        $availableMethods = [];
        foreach ($this->cache->checkAvailability() as $name => $state) {
            if (
                isset($state['available'], $state['functional'])
                && $state['available'] === true
                && $state['functional'] === true
            ) {
                $availableMethods[] = $name;
            }
        }
        return $availableMethods;
    }
}
