<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use JTL\Backend\Permissions;
use JTL\Crawler\Controller;
use JTL\Helpers\Request;
use JTL\Linechart;
use JTL\Pagination\Filter;
use JTL\Pagination\Pagination;
use JTL\Piechart;
use JTL\Smarty\JTLSmarty;
use JTL\Statistik;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Class StatsController
 * @package JTL\Router\Controller\Backend
 */
class StatsController extends AbstractBackendController
{
    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->getText->loadAdminLocale('pages/statistik');
        $statsType = (int)($args['id'] ?? Request::verifyGPCDataInt('s'));
        $crawler   = null;
        if ($statsType === 0) {
            $statsType = \STATS_ADMIN_TYPE_BESUCHER;
        }
        $perm = match ($statsType) {
            2       => Permissions::STATS_VISITOR_LOCATION_VIEW,
            3       => Permissions::STATS_CRAWLER_VIEW,
            4       => Permissions::STATS_EXCHANGE_VIEW,
            5       => Permissions::STATS_LANDINGPAGES_VIEW,
            6       => Permissions::STATS_CONSENT_VIEW,
            default => Permissions::STATS_VISITOR_VIEW,
        };
        $this->checkPermissions($perm);
        $this->route = \str_replace('[/{id}]', '/' . $statsType, $this->route);
        $interval    = 0;
        $filter      = new Filter('statistics');
        $dateRange   = $filter->addDaterangefield(
            \__('Zeitraum'),
            '',
            \date_create()->modify('-1 year')->modify('+1 day')->format('d.m.Y') . ' - ' . \date('d.m.Y'),
            'date'
        );
        $filter->assemble();
        $dateFrom     = \strtotime($dateRange->getStart()) ?: 0;
        $dateUntil    = \strtotime($dateRange->getEnd()) ?: 0;
        $backendStats = $this->getBackendStats($statsType, $dateFrom, $dateUntil, $interval);
        $stats        = $backendStats;
        if ($statsType === \STATS_ADMIN_TYPE_CONSENT) {
            $stats = $backendStats['dataChart'];
        }
        $statsTypeName = $this->geNameByType($statsType);
        $axisNames     = $this->getAxisNames($statsType);
        $pie           = [
            \STATS_ADMIN_TYPE_KUNDENHERKUNFT,
            \STATS_ADMIN_TYPE_SUCHMASCHINE,
            \STATS_ADMIN_TYPE_EINSTIEGSSEITEN
        ];
        if (\in_array($statsType, $pie, true)) {
            $smarty->assign('piechart', $this->preparePieChartStats($stats, $statsTypeName, $axisNames));
        } else {
            $members = $this->getMappingByType($statsType);
            $smarty->assign('linechart', $this->prepareLineChartStats($stats, $statsTypeName, $axisNames))
                ->assign('ymax', $statsType === \STATS_ADMIN_TYPE_CONSENT ? '100' : '')
                ->assign('ymin', '0')
                ->assign('yunit', $statsType === \STATS_ADMIN_TYPE_CONSENT ? ' in %' : '')
                ->assign(
                    'ylabel',
                    $statsType === \STATS_ADMIN_TYPE_CONSENT ? $members['acceptance'] : ($members['nCount'] ?? 0)
                );
        }

        if ($statsType === \STATS_ADMIN_TYPE_CONSENT) {
            $stats = $backendStats['dataTable'];
        }

        if ($statsType === 3) {
            $controller = new Controller($this->db, $this->cache, $this->alertService);
            if (($crawler = $controller->checkRequest()) === false) {
                $crawlerPagination = (new Pagination('crawler'))
                    ->setItemArray($controller->getAllCrawlers())
                    ->assemble();
                $smarty->assign('crawler_arr', $crawlerPagination->getPageItems())
                    ->assign('crawlerPagination', $crawlerPagination);
            }
        }
        $smarty->assign('route', $this->route);

        if ($statsType === 3 && \is_object($crawler)) {
            return $smarty->assign('crawler', $crawler)
                ->getResponse('tpl_inc/crawler_edit.tpl');
        }
        $members = [];
        foreach ($stats as $stat) {
            $members[] = \array_keys(\get_object_vars($stat));
        }

        $pagination = (new Pagination())
            ->setItemCount(\count($stats))
            ->assemble();

        return $smarty->assign('headline', $statsTypeName)
            ->assign('nTyp', $statsType)
            ->assign('oStat_arr', $stats)
            ->assign('cMember_arr', $this->mapData($members, $this->getMappingByType($statsType)))
            ->assign('nPosAb', $pagination->getFirstPageItem())
            ->assign('nPosBis', $pagination->getFirstPageItem() + $pagination->getPageItemCount())
            ->assign('pagination', $pagination)
            ->assign('oFilter', $filter)
            ->getResponse('statistik.tpl');
    }

    /**
     * @param int $type
     * @param int $from
     * @param int $to
     * @param int $intervall
     * @return array<int, object{cEinstiegsseite: string, nCount: int}|object{cReferer: string,
     *      nCount: int}|object{cUserAgent: string, nCount: int}|object{dZeit: string, nCount: int}>
     * @former gibBackendStatistik()
     */
    private function getBackendStats(int $type, int $from, int $to, int &$intervall): array
    {
        if ($type <= 0 || $from <= 0 || $to <= 0) {
            return [];
        }
        $stats     = new Statistik($from, $to);
        $intervall = $stats->getAnzeigeIntervall();

        return match ($type) {
            \STATS_ADMIN_TYPE_BESUCHER        => $stats->holeBesucherStats(),
            \STATS_ADMIN_TYPE_KUNDENHERKUNFT  => $stats->holeKundenherkunftStats(),
            \STATS_ADMIN_TYPE_SUCHMASCHINE    => $stats->holeBotStats(),
            \STATS_ADMIN_TYPE_UMSATZ          => $stats->holeUmsatzStats(),
            \STATS_ADMIN_TYPE_EINSTIEGSSEITEN => $stats->holeEinstiegsseiten(),
            \STATS_ADMIN_TYPE_CONSENT         => $stats->getConsentStats(),
            default                           => [],
        };
    }

    /**
     * @param int $type
     * @return array{nCount: string, dZeit: string,
     *      cReferer?: string, cUserAgent?: string, cEinstiegsseite?: string}|array{}
     */
    private function getMappingByType(int $type): array
    {
        $mapping = [
            \STATS_ADMIN_TYPE_BESUCHER        => [
                'nCount' => \__('count'),
                'dZeit'  => \__('date')
            ],
            \STATS_ADMIN_TYPE_KUNDENHERKUNFT  => [
                'nCount'   => \__('count'),
                'dZeit'    => \__('date'),
                'cReferer' => \__('origin')
            ],
            \STATS_ADMIN_TYPE_SUCHMASCHINE    => [
                'nCount'     => \__('count'),
                'dZeit'      => \__('date'),
                'cUserAgent' => \__('userAgent')
            ],
            \STATS_ADMIN_TYPE_UMSATZ          => [
                'nCount' => \__('amount'),
                'dZeit'  => \__('date')
            ],
            \STATS_ADMIN_TYPE_EINSTIEGSSEITEN => [
                'nCount'          => \__('count'),
                'dZeit'           => \__('date'),
                'cEinstiegsseite' => \__('entryPage')
            ],
            \STATS_ADMIN_TYPE_CONSENT         => [
                'date'       => \__('date'),
                'visitors'   => \__('visitors'),
                'acceptance' => \__('consentAcceptedAll'),
                'consents'   => \__('consentDetails')
            ]
        ];

        return $mapping[$type] ?? [];
    }

    /**
     * @param int $type
     * @return string
     * @former GetTypeNameStats()
     */
    private function geNameByType(int $type): string
    {
        $names = [
            1 => \__('visitor'),
            2 => \__('customerHeritage'),
            3 => \__('searchEngines'),
            4 => \__('sales'),
            5 => \__('entryPages'),
            6 => \__('consent'),
        ];

        return $names[$type] ?? '';
    }

    /**
     * @param int $type
     * @return stdClass
     */
    private function getAxisNames(int $type): stdClass
    {
        $axis    = new stdClass();
        $axis->y = match ($type) {
            \STATS_ADMIN_TYPE_CONSENT => 'acceptance',
            default                   => 'nCount'
        };
        $axis->x = match ($type) {
            \STATS_ADMIN_TYPE_KUNDENHERKUNFT  => 'cReferer',
            \STATS_ADMIN_TYPE_SUCHMASCHINE    => 'cUserAgent',
            \STATS_ADMIN_TYPE_EINSTIEGSSEITEN => 'cEinstiegsseite',
            \STATS_ADMIN_TYPE_CONSENT         => 'date',
            default                           => 'dZeit',
        };

        return $axis;
    }

    /**
     * @param array<int|string, array<int|string, mixed>> $members
     * @param array<string, string>                       $mapping
     * @return array<int|string, array<int|string, mixed>>
     * @former mappeDatenMember()
     */
    private function mapData(array $members, array $mapping): array
    {
        foreach ($members as $i => $data) {
            foreach ($data as $j => $member) {
                $members[$i][$j]    = [];
                $members[$i][$j][0] = $member;
                $members[$i][$j][1] = $mapping[$member];
            }
        }

        return $members;
    }

    /**
     * @param array<int, object{dZeit: string, nCount: int, cReferer?: string, cUserAgent?: string,
     *       cEinstiegsseite?: string, nUmsatz?: float, nAnzahl?: int}> $stats
     * @param string                                                    $name
     * @param stdClass                                                  $axis
     * @param int                                                       $mod
     * @return Linechart
     */
    private function prepareLineChartStats(array $stats, string $name, stdClass $axis, int $mod = 1): Linechart
    {
        $chart = new Linechart(['active' => false]);

        if (\count($stats) === 0) {
            return $chart;
        }
        $chart->setActive(true);
        $data = [];
        $y    = $axis->y;
        $x    = $axis->x;
        foreach ($stats as $j => $stat) {
            $obj    = new stdClass();
            $obj->y = \round((float)$stat->$y, 2, 1);
            if ($j % $mod === 0) {
                $chart->addAxis($stat->$x);
            } else {
                $chart->addAxis('|');
            }

            $data[] = $obj;
        }

        $chart->addSerie($name, $data);
        $chart->memberToJSON();

        return $chart;
    }

    /**
     * @param array<int, object{dZeit: string, nCount: int, cReferer?: string, cUserAgent?: string,
     *       cEinstiegsseite?: string, nUmsatz?: float, nAnzahl?: int}> $stats
     * @param string                                                    $name
     * @param stdClass                                                  $axis
     * @param int                                                       $maxEntries
     * @return Piechart
     */
    private function preparePieChartStats(array $stats, string $name, stdClass $axis, int $maxEntries = 6): Piechart
    {
        $chart = new Piechart(['active' => false]);
        if (\count($stats) === 0) {
            return $chart;
        }
        $chart->setActive(true);
        $data = [];
        $y    = $axis->y;
        $x    = $axis->x;
        // Zeige nur $maxEntries Main Member + 1 Sonstige an, sonst wird es zu unuebersichtlich
        if (\count($stats) > $maxEntries) {
            $statstmp  = [];
            $other     = new stdClass();
            $other->$y = 0;
            $other->$x = \__('miscellaneous');
            foreach ($stats as $i => $stat) {
                if ($i < $maxEntries) {
                    $statstmp[] = $stat;
                } else {
                    $other->$y += $stat->$y;
                }
            }
            $statstmp[] = $other;
            $stats      = $statstmp;
        }

        foreach ($stats as $stat) {
            $value  = \round((float)$stat->$y, 2, 1);
            $data[] = [$stat->$x, $value];
        }

        $chart->addSerie($name, $data);
        $chart->memberToJSON();

        return $chart;
    }
}
