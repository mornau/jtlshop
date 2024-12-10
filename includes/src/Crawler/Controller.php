<?php

declare(strict_types=1);

namespace JTL\Crawler;

use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\Router\Route;
use JTL\Services\JTL\AlertServiceInterface;
use JTL\Shop;
use stdClass;

/**
 * Class Controller
 * @package JTL\Crawler
 */
class Controller
{
    /**
     * Controller constructor.
     * @param DbInterface                $db
     * @param JTLCacheInterface          $cache
     * @param AlertServiceInterface|null $alertService
     */
    public function __construct(
        private readonly DbInterface $db,
        private readonly JTLCacheInterface $cache,
        protected ?AlertServiceInterface $alertService = null
    ) {
    }

    /**
     * @param int $id
     * @return stdClass[]
     * @throws \InvalidArgumentException
     */
    public function getCrawler(int $id): array
    {
        $crawler = $this->db->getObjects(
            'SELECT * FROM tbesucherbot WHERE kBesucherBot = :id ',
            ['id' => $id]
        );
        if (\count($crawler) === 0) {
            throw new \InvalidArgumentException('Provided crawler id ' . $id . ' not found.');
        }

        return $crawler;
    }

    /**
     * @return stdClass[]
     */
    public function getAllCrawlers(): array
    {
        $cacheID = 'crawler';
        /** @var stdClass[]|false $crawlers */
        $crawlers = $this->cache->get($cacheID);
        if ($crawlers === false) {
            $crawlers = $this->db->getObjects('SELECT * FROM tbesucherbot ORDER BY kBesucherBot DESC');
            foreach ($crawlers as $crawler) {
                $crawler->kBesucherBot = (int)$crawler->kBesucherBot;
            }
            $this->cache->set($cacheID, $crawlers, [\CACHING_GROUP_CORE]);
        }

        return $crawlers;
    }

    /**
     * @param string $userAgent
     * @return stdClass|bool
     */
    public function getByUserAgent(string $userAgent): bool|stdClass
    {
        if ($userAgent === '') {
            return false;
        }
        $result = \array_filter($this->getAllCrawlers(), static function ($item) use ($userAgent): bool {
            return $item->cUserAgent !== '' && \mb_stripos($userAgent, $item->cUserAgent) !== false;
        });
        $result = \array_values($result);

        return \count($result) > 0 ? (object)$result[0] : false;
    }

    /**
     * @param string[]|int[] $ids
     * @return bool
     */
    public function deleteCrawler(array $ids): bool
    {
        $this->db->query(
            'DELETE FROM tbesucherbot 
                WHERE kBesucherBot IN (' . \implode(',', \array_map('\intval', $ids)) . ')'
        );
        $this->cache->flush('crawler');

        return true;
    }

    /**
     * @param object $item
     * @return int
     */
    public function saveCrawler(object $item): int
    {
        $this->cache->flush('crawler');
        if (isset($item->cBeschreibung) && !empty($item->kBesucherBot)) {
            return $this->db->update(
                'tbesucherbot',
                'kBesucherBot',
                $item->kBesucherBot,
                $item
            );
        }

        return $this->db->insert('tbesucherbot', $item);
    }

    /**
     * @return Crawler|false
     */
    public function checkRequest(): bool|Crawler
    {
        if (
            Form::validateToken() === false
            && (Request::pInt('save_crawler') || Request::postInt('delete_crawler'))
        ) {
            $this->alertService?->addError(\__('errorCSRF'), 'errorCSRF');

            return false;
        }
        if (Request::pInt('delete_crawler') === 1) {
            /** @var string[] $selected */
            $selected = Request::postVar('selectedCrawler');
            $this->deleteCrawler($selected);
        }
        if (Request::pInt('save_crawler') === 1) {
            $this->save();
        }
        $crawler = false;
        if (Request::verifyGPCDataInt('edit') === 1 || Request::verifyGPCDataInt('new') === 1) {
            $crawler = new Crawler();
            if (($crawlerID = Request::verifyGPCDataInt('id')) > 0) {
                $crawler->map($this->getCrawler($crawlerID));
            }
        }

        return $crawler;
    }

    private function save(): void
    {
        if (empty(Request::postVar('useragent')) || empty(Request::postVar('description'))) {
            $this->alertService?->addError(\__('missingCrawlerFields'), 'missingCrawlerFields');

            return;
        }
        $item                = new stdClass();
        $item->kBesucherBot  = Request::pInt('id');
        $item->cUserAgent    = Text::filterXSS(Request::postVar('useragent'));
        $item->cBeschreibung = Text::filterXSS(Request::postVar('description'));
        $result              = $this->saveCrawler($item);
        if ($result === -1) {
            $this->alertService?->addError(\__('missingCrawlerFields'), 'missingCrawlerFields');
        } else {
            \header('Location: ' . Shop::getAdminURL() . '/' . Route::STATS . '/3?tab=settings');
            exit;
        }
    }

    /**
     * @return int
     */
    public function getRequestBot(): int
    {
        $bot = $this->getByUserAgent($_SERVER['HTTP_USER_AGENT'] ?? '');

        return (int)($bot->kBesucherBot ?? 0);
    }

    /**
     * @param string $file
     * @return null|string
     */
    public function getRequestFile(string $file): ?string
    {
        $pathInfo = \pathinfo($file);
        if (!isset($pathInfo['extension']) || !\in_array($pathInfo['extension'], ['xml', 'txt', 'gz'], true)) {
            return null;
        }
        if ($file !== $pathInfo['basename']) {
            return null;
        }

        return \file_exists(\PFAD_ROOT . \PFAD_EXPORT . $file)
            ? $file
            : null;
    }

    /**
     * @param string $file
     */
    public function sendRequestFile(string $file): void
    {
        $file         = \basename($file);
        $absoluteFile = \PFAD_ROOT . \PFAD_EXPORT . \basename($file);
        $extension    = \pathinfo($absoluteFile, \PATHINFO_EXTENSION);
        $contentType  = match (\mb_convert_case($extension, \MB_CASE_LOWER)) {
            'xml'   => 'application/xml',
            'txt'   => 'text/plain',
            default => 'application/octet-stream',
        };

        if (\file_exists($absoluteFile)) {
            \header('Content-Type: ' . $contentType);
            \header('Content-Length: ' . \filesize($absoluteFile));
            \header('Last-Modified: ' . \gmdate('D, d M Y H:i:s', \filemtime($absoluteFile) ?: null) . ' GMT');

            if ($contentType === 'application/octet-stream') {
                \header('Content-Description: File Transfer');
                \header('Content-Disposition: attachment; filename=' . $file);
                \header('Content-Transfer-Encoding: binary');
            }

            \ob_end_clean();
            \flush();
            \readfile($absoluteFile);
            exit;
        }
    }

    public function getResponse(): void
    {
        /** @var string $param */
        $param    = Request::getVar('datei', '');
        $fileName = $this->getRequestFile($param);
        if ($fileName === null) {
            \http_response_code(503);
            \header('Retry-After: 86400');
            exit;
        }
        $ip              = Request::getRealIP();
        $floodProtection = $this->db->getAffectedRows(
            'SELECT * 
                FROM `tsitemaptracker` 
                WHERE `cIP` = :ip 
                    AND DATE_ADD(`dErstellt`, INTERVAL 2 MINUTE) >= NOW() 
                ORDER BY `dErstellt` DESC',
            ['ip' => $ip]
        );
        if ($floodProtection === 0) {
            $sitemapTracker               = new stdClass();
            $sitemapTracker->cSitemap     = \basename($fileName);
            $sitemapTracker->kBesucherBot = $this->getRequestBot();
            $sitemapTracker->cIP          = $ip;
            $sitemapTracker->cUserAgent   = Text::filterXSS($_SERVER['HTTP_USER_AGENT'] ?? '');
            $sitemapTracker->dErstellt    = 'NOW()';

            $this->db->insert('tsitemaptracker', $sitemapTracker);
        }
        $this->sendRequestFile($fileName);
    }
}
