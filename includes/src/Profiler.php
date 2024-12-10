<?php

declare(strict_types=1);

namespace JTL;

use stdClass;

/**
 * Class Profiler
 * @package JTL
 */
class Profiler
{
    /**
     * @var bool
     */
    public static bool $functional = false;

    /**
     * @var bool
     */
    public static bool $enabled = false;

    /**
     * @var bool
     */
    public static bool $started = false;

    /**
     * @var array
     */
    public static array $data = [];

    /**
     * @var string
     */
    public static string $dataDir = '/tmp';

    /**
     * @var int
     */
    public static int $flags = -1;

    /**
     * @var array
     */
    public static array $options = [];

    /**
     * @var object
     */
    public static $run;

    /**
     * set to true to finish profiling
     * used to not save sql statements created by the profiler itself
     *
     * @var bool
     */
    private static bool $stopProfiling = false;

    /**
     * @var stdClass[]
     */
    private static array $sqlProfile = [];

    /**
     * @var array
     */
    private static array $sqlErrors = [];

    /**
     * @var array<string, array{success: string[], failure: string[]}>
     */
    private static array $cacheProfile = [
        'options' => [],
        'get'     => ['success' => [], 'failure' => []],
        'set'     => ['success' => [], 'failure' => []],
        'flush'   => ['success' => [], 'failure' => []],
    ];

    /**
     * @var null|string
     */
    public static ?string $method = null;

    /**
     * check if one of the profilers is active
     *
     * @return int
     * 0: none
     * 1: NiceDB profiler
     * 2: xhprof
     * 6: DB, xhprof
     * 7: all
     */
    public static function getIsActive(): int
    {
        if (\PROFILE_QUERIES !== false && \PROFILE_SHOP === true) {
            return 6;
        }
        if (\PROFILE_SHOP === true) {
            return 2;
        }
        if (\PROFILE_QUERIES !== false) {
            return 1;
        }

        return 0;
    }

    /**
     * @param string $action
     * @param string $status
     * @param mixed  $key
     */
    public static function setCacheProfile(string $action, string $status, $key): void
    {
        self::$cacheProfile[$action][$status][] = $key;
    }

    /**
     * set plugin profiler run
     *
     * @param mixed $data
     * @return bool
     * @deprecated since 5.3.0
     */
    public static function setPluginProfile($data): bool
    {
        return false;
    }

    /**
     * set sql profiler run
     *
     * @param stdClass $data
     * @return bool
     */
    public static function setSQLProfile(stdClass $data): bool
    {
        if (self::$stopProfiling !== false) {
            return false;
        }
        self::$sqlProfile[] = $data;

        return true;
    }

    /**
     * set sql profiler run
     *
     * @param mixed $data
     * @return bool
     */
    public static function setSQLError($data): bool
    {
        if (self::$stopProfiling !== false) {
            return false;
        }
        self::$sqlErrors[] = $data;

        return true;
    }

    /**
     * save sql profiler run to DB
     *
     * @return bool
     */
    public static function saveSQLProfile(): bool
    {
        self::$stopProfiling = true;
        if (\PROFILE_QUERIES_ECHO === true || \count(self::$sqlProfile) === 0) {
            return false;
        }
        // create run object
        $run        = new stdClass();
        $run->url   = $_SERVER['REQUEST_URI'] ?? '';
        $run->ptype = 'sql';
        // build stats for this run
        $run->total_count = 0; //total number of queries
        $run->total_time  = 0.0; //total execution time
        // filter duplicated queries
        $filtered = [];
        foreach (self::$sqlProfile as $queryRun) {
            $item = $filtered[$queryRun->hash] ?? null;
            if ($item === null) {
                $obj                       = new stdClass();
                $obj->runtime              = $queryRun->time;
                $obj->runcount             = $queryRun->count;
                $obj->statement            = \trim($queryRun->statement ?? '');
                $obj->tablename            = $queryRun->table;
                $obj->data                 = isset($queryRun->backtrace)
                    ? \serialize(['backtrace' => $queryRun->backtrace])
                    : null;
                $filtered[$queryRun->hash] = $obj;
            } else {
                $item->runtime += $queryRun->time;
                ++$item->runcount;
            }
            $run->total_time += $queryRun->time;
            ++$run->total_count;
        }
        // insert profiler run into DB - return a new primary key
        $db    = Shop::Container()->getDB();
        $runID = $db->insert('tprofiler', $run);
        if (!\is_numeric($runID)) {
            return false;
        }
        // set runID for all filtered queries and save to DB
        foreach ($filtered as $queryRun) {
            $queryRun->runID = $runID;
            $db->insert('tprofiler_runs', $queryRun);
        }
        foreach (self::$sqlErrors as $_error) {
            $queryRun            = new stdClass();
            $queryRun->runID     = $runID;
            $queryRun->tablename = 'error';
            $queryRun->runtime   = 0;
            $queryRun->statement = \trim($_error->statement);
            $queryRun->data      = \serialize(['message' => $_error->message, 'backtrace' => $_error->backtrace]);
            $db->insert('tprofiler_runs', $queryRun);
        }

        return true;
    }

    /**
     * save plugin profiler run to DB
     *
     * @return bool
     * @deprecated since 5.3.0
     */
    public static function savePluginProfile(): bool
    {
        return false;
    }

    /**
     * return all the sql profile data currently collected
     * for the use in plugins like JTLDebug
     *
     * @return array
     */
    public static function getCurrentSQLProfile(): array
    {
        return self::$sqlProfile;
    }

    /**
     * return all the plugin profile data currently collected
     * for the use in plugins like JTLDebug
     *
     * @return array
     * @deprecated since 5.3.0
     */
    public static function getCurrentPluginProfile(): array
    {
        return [];
    }

    /**
     * return all the cache profile data currently collected
     * for the use in plugins like JTLDebug
     *
     * @return array
     */
    public static function getCurrentCacheProfile(): array
    {
        return self::$cacheProfile;
    }

    /**
     * get plugin profiler data from DB
     *
     * @param bool $combined
     * @return array
     * @deprecated since 5.3.0
     */
    public static function getPluginProfiles(bool $combined = false): array
    {
        return self::getProfile('plugin', $combined);
    }

    /**
     * @param bool $combined
     * @return array
     */
    public static function getSQLProfiles(bool $combined = false): array
    {
        return self::getProfile('sql', $combined);
    }

    /**
     * generic profiler getter
     *
     * @param string $type
     * @param bool   $combined
     * @return array
     */
    private static function getProfile(string $type = 'plugin', bool $combined = false): array
    {
        $db = Shop::Container()->getDB();
        if ($combined === true) {
            return $db->getObjects(
                'SELECT *
                    FROM tprofiler
                    JOIN tprofiler_runs 
                        ON tprofiler.runID = tprofiler_runs.runID
                    WHERE ptype = :type
                    ORDER BY tprofiler.runID DESC',
                ['type' => $type]
            );
        }
        $profiles = $db->selectAll('tprofiler', 'ptype', $type, '*', 'runID DESC');
        $data     = [];
        foreach ($profiles as $profile) {
            $profile->data = $db->selectAll(
                'tprofiler_runs',
                'runID',
                (int)$profile->runID,
                '*',
                'runtime DESC'
            );
            $data[]        = $profile;
        }

        return $data;
    }

    /**
     * @param int    $flags
     * @param array  $options
     * @param string $dir
     * @return bool
     */
    public static function start(int $flags = -1, array $options = [], string $dir = '/tmp'): bool
    {
        if (\defined('PROFILE_SHOP') && PROFILE_SHOP === true) {
            self::$flags   = $flags;
            self::$options = $options;
            self::$dataDir = $dir;
            self::$enabled = true;
            if (\function_exists('xhprof_enable')) {
                self::$method = 'xhprof';
                if (self::$flags === -1) {
                    self::$flags = \XHPROF_FLAGS_CPU + \XHPROF_FLAGS_MEMORY;
                }
                \xhprof_enable(self::$flags, self::$options);
            } elseif (\function_exists('tideways_enable')) {
                self::$method = 'tideways';
                if (self::$flags === -1) {
                    self::$flags = \TIDEWAYS_FLAGS_CPU | \TIDEWAYS_FLAGS_MEMORY | \TIDEWAYS_FLAGS_NO_SPANS;
                }
                \tideways_enable(self::$flags);
            } elseif (\function_exists('tideways_xhprof_enable')) {
                self::$method = 'tideways5';
                if (self::$flags === -1) {
                    self::$flags = \TIDEWAYS_XHPROF_FLAGS_MEMORY | \TIDEWAYS_XHPROF_FLAGS_CPU;
                }
                \tideways_xhprof_enable(self::$flags);
            }
        }
        self::$functional = self::$method !== null;
        self::$started    = self::$method !== null;

        return self::$enabled && self::$functional;
    }

    /**
     * @return bool
     */
    public static function getIsStarted(): bool
    {
        return self::$started;
    }

    /**
     * @return bool
     */
    public static function finish(): bool
    {
        if (self::$enabled !== true || self::$functional !== true) {
            return false;
        }
        self::$data = self::$method === 'xhprof'
            ? \xhprof_disable()
            : (self::$method === 'tideways'
                ? \tideways_disable()
                : \tideways_xhprof_disable());

        return true;
    }

    /**
     * @return array
     */
    public static function getData(): array
    {
        $html  = '';
        $runID = 0;
        if (self::$enabled === true && self::$functional === true) {
            require_once \PFAD_ROOT . 'xhprof_lib/utils/xhprof_lib.php';
            require_once \PFAD_ROOT . 'xhprof_lib/utils/xhprof_runs.php';
            if (self::$method === 'xhprof') {
                self::$run = new \XHProfRuns_Default('/tmp');
                $runID     = self::$run->save_run(self::$data, 'xhprof_jtl');
            } else {
                $runID    = \uniqid('', true);
                $filename = \sys_get_temp_dir() . '/' . $runID . '.xhprof_jtl.xhprof';
                \file_put_contents($filename, \serialize(self::$data));
            }
            $html = '<div class="profile-wrapper" style="position:fixed;z-index:9999;bottom:5px;left:5px;">
                        <a class="btn btn-danger" target="_blank" rel="nofollow" href="' .
                Shop::getURL() . '/xhprof_html/index.php?run=' . $runID . '&source=xhprof_jtl&sort=excl_wt">
                        View profile
                        </a>
                    </div>';
        }

        return [
            'html'   => $html,
            'run'    => self::$run,
            'run_id' => $runID
        ];
    }

    /**
     * output sql profiler data
     */
    public static function output(): void
    {
        if (\PROFILE_QUERIES_ECHO !== true || \count(self::$sqlProfile) === 0) {
            return;
        }
        $totalQueries = 0;
        $inserts      = 0;
        $errors       = \count(self::$sqlErrors);
        foreach (self::$sqlProfile as $query) {
            if ($query->type === 'INSERT') {
                ++$inserts;
            }
            ++$totalQueries;
        }
        if (\defined('FILTER_SQL_QUERIES') && \FILTER_SQL_QUERIES === true) {
            $hashes           = [];
            self::$sqlProfile = \array_filter(self::$sqlProfile, static function (stdClass $e) use (&$hashes): bool {
                if (!\in_array($e->hash, $hashes, true)) {
                    $hashes[] = $e->hash;

                    return true;
                }

                return false;
            });
            \uasort(self::$sqlProfile, static function (stdClass $a, stdClass $b): int {
                return $b->time <=> $a->time;
            });
        }
        echo '
            <style>
                #pfdbg{
                    max-width:99%;opacity:0.85;position:absolute;z-index:999999;
                    background:#efefef;top:50px;left:10px;padding:10px;font-size:11px;
                    border:1px solid black;box-shadow:1px 1px 3px rgba(0,0,0,0.4);border-radius:3px;
                }
                #dbg-close{
                    float:right;
                }
                .sql-statement{
                    white-space: pre-wrap;
                    word-wrap: break-word;
                }
            </style>
            <div id="pfdbg">' .
            '<button id="dbg-close" class="btn btn-close" onclick="$(\'#pfdbg\').hide();return false;">X</button>' .
            '<strong>Total Queries:</strong> ' . $totalQueries .
            '<br><strong>Inserts:</strong> ' . $inserts .
            '<br><strong>Errors:</strong> ' . $errors .
            '<br><strong>Statements:</strong> ' .
            '<ul class="sql-tables-list">';
        foreach (self::$sqlProfile as $query) {
            echo '<li class="sql-table"><span class="table-name">' .
                $query->table .
                '</span> (' . $query->time . 's)';
            if (isset($query->statement)) {
                echo '<pre class="sql-statement">' . $query->statement . '</pre>';
            }
            if (!empty($query->backtrace)) {
                echo '<ul class="backtrace">';
                foreach ($query->backtrace as $_bt) {
                    echo '<li class="backtrace-item">' .
                        $_bt['file'] . ':' . $_bt['line'] . ' - ' . (isset($_bt['class'])
                            ? ($_bt['class'] . '::')
                            : '') . $_bt['function'] . '()' .
                        '</li>';
                }
                echo '</ul>';
            }
        }
        echo '</ul>';
        if ($errors > 0) {
            echo '<br><strong>Errors:</strong> ' .
                '<ul class="sql-tables-list">';
            foreach (self::$sqlErrors as $_error) {
                echo '<li>' .
                    $_error->message .
                    ' for query <pre class="sql-statement">' . $_error->statement . '</pre></li>';
            }
            echo '</ul>';
        }
        echo '</div>';
    }

    public static function finalize(): void
    {
        self::$stopProfiling = true;
        self::saveSQLProfile();
        self::output();
        if (self::getIsStarted() === true) {
            self::finish();
            $data = self::getData();
            echo $data['html'];
        }
    }
}
