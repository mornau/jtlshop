<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use JTL\Backend\Permissions;
use JTL\Backend\Settings\Manager;
use JTL\DB\ReturnType;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Jtllog;
use JTL\Pagination\DataType;
use JTL\Pagination\Filter;
use JTL\Pagination\Operation;
use JTL\Pagination\Pagination;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class SystemLogController
 * @package JTL\Router\Controller\Backend
 */
class SystemLogController extends AbstractBackendController
{
    private Manager $settingManager;

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::SYSTEMLOG_VIEW);
        $this->getText->loadAdminLocale('pages/systemlog');

        $minLogLevel          = Shop::getSettingValue(\CONF_GLOBAL, 'systemlog_flag');
        $this->settingManager = new Manager($this->db, $smarty, $this->account, $this->getText, $this->alertService);
        if (Form::validateToken()) {
            $this->handleUpdates();
        }
        $filter        = new Filter('syslog');
        $selectedLevel = $this->addLevelSelect($filter);
        $this->addChannelFilter($filter);
        $filter->addDaterangefield(\__('Zeitraum'), 'dErstellt', '', 'date');
        $searchfield = $filter->addTextfield(
            \__('systemlogSearch'),
            'cLog',
            Operation::CONTAINS,
            DataType::TEXT,
            'search'
        );
        $filter->assemble();
        /** @var string $searchString */
        $searchString = $searchfield->getValue();
        $this->getPagination($searchString, $selectedLevel, $filter);
        $this->addSettingsLogFilter();
        $this->preparePluginStateLog();
        $this->prepareTemplateLog();

        return $smarty->assign('oFilter', $filter)
            ->assign('minLogLevel', $minLogLevel)
            ->assign('nTotalLogCount', Jtllog::getLogCount())
            ->assign('route', $this->route)
            ->getResponse('systemlog.tpl');
    }

    private function addChannelFilter(Filter $filter): void
    {
        $channels = $this->db->getObjects(
            'SELECT DISTINCT(tjtllog.cKey) AS value, tplugin.cName AS name
                FROM tjtllog
                LEFT JOIN tplugin
                ON tplugin.cPluginID = cKey
                WHERE cKey != \'kPlugin\''
        );
        if (\count($channels) === 0) {
            return;
        }
        $channelSelect = $filter->addSelectfield(\__('Channel'), 'cKey', 0, 'channel');
        $channelSelect->addSelectOption(\__('all'), Operation::CUSTOM);
        foreach ($channels as $channel) {
            if ($channel->name === null && $channel->value === 'jtllog') {
                $channelSelect->addSelectOption('Core', $channel->value, Operation::EQUALS);
            } elseif ($channel->name !== null) {
                $channelSelect->addSelectOption($channel->name, $channel->value, Operation::EQUALS);
            }
        }
    }

    private function addLevelSelect(Filter $filter): int
    {
        $levelSelect = $filter->addSelectfield(\__('systemlogLevel'), 'nLevel', 0, 'level');
        $levelSelect->addSelectOption(\__('all'), Operation::CUSTOM);
        $levelSelect->addSelectOption(\__('systemlogDebug'), Logger::DEBUG, Operation::EQUALS);
        $levelSelect->addSelectOption(\__('systemlogNotice'), Logger::INFO, Operation::EQUALS);
        $levelSelect->addSelectOption(\__('systemlogWarning'), Logger::WARNING, Operation::EQUALS);
        $levelSelect->addSelectOption(\__('systemlogError'), Logger::ERROR, Operation::GREATER_THAN_EQUAL);
        $selectedLevel = $levelSelect->getSelectedOption()?->getValue() ?? 0;

        return (int)$selectedLevel;
    }

    private function getPagination(string $searchString, int $selectedLevel, Filter $filter): void
    {
        $filteredLogCount = Jtllog::getLogCount($searchString, $selectedLevel, $filter->getWhereSQL());
        $pagination       = (new Pagination('syslog'))
            ->setItemsPerPageOptions([10, 20, 50, 100, -1])
            ->setItemCount($filteredLogCount)
            ->assemble();
        $logData          = Jtllog::getLogWhere($filter->getWhereSQL(), $pagination->getLimitSQL());
        foreach ($logData as $log) {
            $log->kLog   = (int)$log->kLog;
            $log->nLevel = (int)$log->nLevel;
            $log->cLog   = \preg_replace(
                '/\[(.*)] => (.*)/',
                '<span class="text-primary">$1</span>: <span class="text-success">$2</span>',
                $log->cLog
            );
            if (!empty($searchString)) {
                $log->cLog = \preg_replace(
                    '/(' . \preg_quote($searchString, '/') . ')/i',
                    '<mark>$1</mark>',
                    $log->cLog
                );
            }
        }
        $this->getSmarty()->assign('pagination', $pagination)
            ->assign('logs', $logData);
    }

    private function addSettingsLogFilter(): void
    {
        $settingLogsFilter = new Filter('settingsLog');
        $settingLogsFilter->addDaterangefield(
            \__('Zeitraum'),
            'dDatum',
            \date_create()->modify('-1 month')->format('d.m.Y') . ' - ' . \date('d.m.Y'),
            'date'
        );
        $settingLogsFilter->assemble();
        $settingLogsPagination = (new Pagination('settingsLog'))
            ->setItemCount($this->settingManager->getAllSettingLogsCount($settingLogsFilter->getWhereSQL()))
            ->assemble();
        $this->getSmarty()->assign('settingLogsPagination', $settingLogsPagination)
            ->assign('settingLogsFilter', $settingLogsFilter)
            ->assign(
                'settingLogs',
                $this->settingManager->getAllSettingLogs(
                    $settingLogsFilter->getWhereSQL(),
                    $settingLogsPagination->getLimitSQL()
                )
            );
    }

    private function handleUpdates(): void
    {
        if (Request::verifyGPDataString('action') === 'clearsyslog') {
            Jtllog::deleteAll();
            $this->alertService->addSuccess(\__('successSystemLogReset'), 'successSystemLogReset');
        } elseif (Request::verifyGPDataString('action') === 'save') {
            $minLogLevel = (int)($_POST['minLogLevel'] ?? 0);
            $this->db->update(
                'teinstellungen',
                'cName',
                'systemlog_flag',
                (object)['cWert' => $minLogLevel]
            );
            $this->cache->flushTags([\CACHING_GROUP_OPTION]);
            $this->alertService->addSuccess(\__('successConfigSave'), 'successConfigSave');
            $this->getSmarty()->assign('cTab', 'config');
        } elseif (Request::verifyGPDataString('action') === 'delselected') {
            if (isset($_POST['selected'])) {
                $this->alertService->addSuccess(
                    Jtllog::deleteIDs($_POST['selected']) . \__('successEntriesDelete'),
                    'successEntriesDelete'
                );
            }
        }
    }

    /**
     * @return void
     */
    protected function preparePluginStateLog(): void
    {
        $plugins = $this->db->selectAll('tplugin', [], [], 'kPlugin, cName', 'cName');
        $filter  = new Filter('pluginlog');
        $filter->addDaterangefield(\__('Zeitraum'), 'timestamp', '', 'plgndate');
        $pluginSelect = $filter->addSelectfield(\__('plugin'), 'pluginID', 0, 'pluginid');
        $pluginSelect->addSelectOption(\__('all'), 0, Operation::NOT_EQUAL);

        foreach ($plugins as $plugin) {
            $pluginSelect->addSelectOption($plugin->cName, $plugin->kPlugin, Operation::EQUALS);
        }
        $filter->assemble();
        $where = $filter->getWhereSQL();

        $totalEntryCount = $this->db->getSingleInt(
            'SELECT COUNT(id) AS count FROM plugin_state_log WHERE ' . $filter->getWhereSQL(),
            'count'
        );

        $pagination = (new Pagination('pluginlog'))
            ->setItemCount($totalEntryCount)
            ->setSortByOptions([['timestamp', 'Zeit']])
            ->setDefaultSortByDir(1)
            ->assemble();

        $order     = $pagination->getOrderSQL();
        $limit     = $pagination->getLimitSQL();
        $pluginLog = $this->db->getObjects(
            'SELECT plugin_state_log.*, tadminlogin.cName as adminName
            FROM plugin_state_log
                LEFT JOIN tadminlogin ON tadminlogin.kAdminlogin = plugin_state_log.adminloginID' .
            ($where !== '' ? ' WHERE ' . $where : '') .
            ($order !== '' ? ' ORDER BY ' . $order : '') .
            ($limit !== '' ? ' LIMIT ' . $limit : '')
        );

        $this->smarty->assign('pluginStateMapper', Shop::Container()->getPluginState())
            ->assign('pluginLogPagination', $pagination)
            ->assign('pluginLogFilter', $filter)
            ->assign('pluginLog', $pluginLog);
    }

    /**
     * @return void
     */
    protected function prepareTemplateLog(): void
    {
        $filter = new Filter('templatelog');
        $filter->addDaterangefield(\__('Zeitraum'), 'timestamp');
        $filter->assemble();
        $where = $filter->getWhereSQL();

        $totalEntryCount = $this->db->getSingleInt(
            'SELECT COUNT(id) AS count FROM template_settings_log ' . ($where !== '' ? ' WHERE ' . $where : ''),
            'count'
        );

        $pagination = (new Pagination('templatelog'))
            ->setItemCount($totalEntryCount)
            ->setSortByOptions([['timestamp', 'Zeit']])
            ->setDefaultSortByDir(1)
            ->assemble();

        $order       = $pagination->getOrderSQL();
        $limit       = $pagination->getLimitSQL();
        $templateLog = $this->db->query(
            'SELECT template_settings_log.*, tadminlogin.cName as adminName
            FROM template_settings_log
                LEFT JOIN tadminlogin ON tadminlogin.kAdminlogin = template_settings_log.adminloginID' .
            ($where !== '' ? ' WHERE ' . $where : '') .
            ($order !== '' ? ' ORDER BY ' . $order : '') .
            ($limit !== '' ? ' LIMIT ' . $limit : ''),
            ReturnType::ARRAY_OF_OBJECTS
        );

        $this->smarty
            ->assign('templateLogPagination', $pagination)
            ->assign('templateLogFilter', $filter)
            ->assign('templateLog', $templateLog);
    }
}
