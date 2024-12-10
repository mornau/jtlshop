<?php

declare(strict_types=1);

namespace JTL;

use JTL\DB\DbInterface;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\Helpers\URL;
use JTL\Settings\Option\Globals;
use JTL\Settings\Settings;
use stdClass;

/**
 * Class Redirect
 * @package JTL
 */
class Redirect
{
    public ?int $kRedirect = null;

    public ?string $cFromUrl = null;

    public ?string $cToUrl = null;

    public ?string $cAvailable = null;

    public int $type = self::TYPE_UNKNOWN;

    public int $nCount = 0;

    public int $paramHandling = 0;

    protected DbInterface $db;

    public const TYPE_UNKNOWN = 0;
    public const TYPE_WAWI    = 1;
    public const TYPE_IMPORT  = 2;
    public const TYPE_MANUAL  = 3;
    public const TYPE_404     = 4;

    /**
     * @param int              $id
     * @param DbInterface|null $db
     */
    public function __construct(int $id = 0, ?DbInterface $db = null)
    {
        $this->db = $db ?? Shop::Container()->getDB();
        if ($id > 0) {
            $this->loadFromDB($id);
        }
    }

    /**
     * @param int $id
     * @return $this
     */
    public function loadFromDB(int $id): self
    {
        $obj = $this->db->select('tredirect', 'kRedirect', $id);
        if ($obj === null || $obj->kRedirect < 1) {
            return $this;
        }
        $this->kRedirect     = (int)$obj->kRedirect;
        $this->nCount        = (int)$obj->nCount;
        $this->paramHandling = (int)$obj->paramHandling;
        $this->cFromUrl      = $obj->cFromUrl;
        $this->cToUrl        = $obj->cToUrl;
        $this->cAvailable    = $obj->cAvailable;
        $this->type          = (int)($obj->type ?? self::TYPE_UNKNOWN);

        return $this;
    }

    /**
     * @param string $url
     * @return null|stdClass
     */
    public function find(string $url): ?stdClass
    {
        return $this->db->select(
            'tredirect',
            'cFromUrl',
            \mb_substr($this->normalize($url), 0, 255)
        );
    }

    /**
     * Get a redirect by target
     *
     * @param string $targetURL target to search for
     * @return null|stdClass
     */
    public function getRedirectByTarget(string $targetURL): ?stdClass
    {
        return $this->db->select('tredirect', 'cToUrl', $this->normalize($targetURL));
    }

    /**
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public function isDeadlock(string $source, string $destination): bool
    {
        $path        = \parse_url(Shop::getURL(), \PHP_URL_PATH);
        $destination = $path !== null ? ($path . '/' . $destination) : $destination;
        $redirect    = $this->db->select('tredirect', 'cFromUrl', $destination, 'cToUrl', $source);

        return $redirect !== null && (int)$redirect->kRedirect > 0;
    }

    public function saveExt(
        string $source,
        string $destination,
        bool $force = false,
        int $handling = 0,
        bool $overwriteExisting = false,
        int $type = self::TYPE_UNKNOWN
    ): bool {
        if (\mb_strlen($source) > 0) {
            $source = $this->normalize($source);
        }
        if (\mb_strlen($destination) > 0) {
            $destination = $this->normalize($destination);
        }
        if ($source === $destination) {
            return false;
        }

        $oldRedirects = $this->db->getObjects(
            'SELECT * FROM tredirect WHERE cToUrl = :source',
            ['source' => $source]
        );
        foreach ($oldRedirects as $oldRedirect) {
            $oldRedirect->cToUrl = $destination;
            if ($oldRedirect->cFromUrl === $destination) {
                $this->db->delete('tredirect', 'kRedirect', (int)$oldRedirect->kRedirect);
            } else {
                $this->db->updateRow('tredirect', 'kRedirect', (int)$oldRedirect->kRedirect, $oldRedirect);
            }
        }

        if (
            $force
            || (self::checkAvailability($destination)
                && \mb_strlen($source) > 1
                && \mb_strlen($destination) > 1)
        ) {
            if ($this->isDeadlock($source, $destination)) {
                $this->db->delete('tredirect', ['cToUrl', 'cFromUrl'], [$source, $destination]);
            }
            $target = $this->getRedirectByTarget($source);
            if ($target !== null) {
                $this->saveExt($target->cFromUrl, $destination, false, $handling, false, $type);
                $ins                = new stdClass();
                $ins->cToUrl        = Text::convertUTF8($destination);
                $ins->cAvailable    = 'y';
                $ins->paramHandling = $handling;
                $ins->type          = $type;
                $this->db->update('tredirect', 'cToUrl', $source, $ins);
            }

            $redirect = $this->find($source);
            if ($redirect === null) {
                $ins                = new stdClass();
                $ins->cFromUrl      = Text::convertUTF8($source);
                $ins->cToUrl        = Text::convertUTF8($destination);
                $ins->cAvailable    = 'y';
                $ins->paramHandling = $handling;
                $ins->type          = $type;
                if ($this->db->insert('tredirect', $ins) > 0) {
                    return true;
                }
            } elseif (
                ($overwriteExisting || empty($redirect->cToUrl))
                && $this->normalize($redirect->cFromUrl) === $source
            ) {
                // the redirect already exists with empty cToUrl or updateExisting is allowed => update
                $update = $this->db->update(
                    'tredirect',
                    'cFromUrl',
                    $source,
                    (object)['cToUrl' => Text::convertUTF8($destination), 'type' => $type]
                );

                return $update > 0;
            }
        }

        return false;
    }

    /**
     * @param string $url
     * @return string|false
     */
    public function test(string $url): bool|string
    {
        $url = $this->normalize($url);
        if (\mb_strlen($url) === 0 || !$this->isValid($url)) {
            return false;
        }
        $redirectUrl = false;
        $parsedUrl   = \parse_url($url);
        $queryString = null;
        if (isset($parsedUrl['query'], $parsedUrl['path'])) {
            $url         = $parsedUrl['path'];
            $queryString = $parsedUrl['query'];
        }
        $foundRedirectWithQuery = false;
        if (!empty($queryString)) {
            $item = $this->find($url . '?' . $queryString);
            if ($item !== null) {
                $url                    .= '?' . $queryString;
                $foundRedirectWithQuery = true;
            } else {
                $item = $this->find($url);
                if ($item !== null) {
                    if ((int)$item->paramHandling === 0) {
                        $item = null;
                    } elseif ((int)$item->paramHandling === 1) {
                        $foundRedirectWithQuery = true;
                    }
                }
            }
        } else {
            $item = $this->find($url);
        }
        if ($item === null) {
            if (!isset($_GET['notrack']) && Settings::boolValue(Globals::REDIRECTS_404)) {
                $item             = new stdClass();
                $item->cFromUrl   = $url . (!empty($queryString) ? '?' . $queryString : '');
                $item->cToUrl     = '';
                $item->cAvailable = '';
                $item->nCount     = 0;
                $item->type       = self::TYPE_404;
                unset($item->kRedirect);
                $item->kRedirect = $this->db->insert('tredirect', $item);
            }
        } elseif (\mb_strlen($item->cToUrl) > 0) {
            $redirectUrl = $item->cToUrl;
            $redirectUrl .= $queryString !== null && !$foundRedirectWithQuery
                ? '?' . $queryString
                : '';
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (\mb_strlen($referer) > 0) {
            $referer = $this->normalize($referer);
        }
        $ip = Request::getRealIP();
        // Eintrag für diese IP bereits vorhanden?
        $entry = $this->db->getSingleObject(
            'SELECT *
                FROM tredirectreferer tr
                LEFT JOIN tredirect t
                    ON t.kRedirect = tr.kRedirect
                WHERE tr.cIP = :ip
                AND t.cFromUrl = :frm LIMIT 1',
            ['ip' => $ip, 'frm' => $url]
        );
        if ($entry === null || (\is_object($entry) && (int)$entry->nCount === 0)) {
            $ins               = new stdClass();
            $ins->kRedirect    = $item !== null ? $item->kRedirect : 0;
            $ins->kBesucherBot = (int)($_SESSION['oBesucher']->kBesucherBot ?? 0);
            $ins->cRefererUrl  = \is_string($referer) ? $referer : '';
            $ins->cIP          = $ip;
            $ins->dDate        = \time();
            $this->db->insert('tredirectreferer', $ins);
            // this counts only how many different referrers are hitting that url
            if ($item !== null) {
                ++$item->nCount;
                $this->db->update('tredirect', 'kRedirect', $item->kRedirect, $item);
            }
        }

        return $redirectUrl;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function isValid(string $url): bool
    {
        $extension         = \pathinfo($url, \PATHINFO_EXTENSION);
        $invalidExtensions = [
            'jpg',
            'gif',
            'bmp',
            'xml',
            'ico',
            'txt',
            'png'
        ];
        if (\mb_strlen($extension) > 0) {
            $extension = \mb_convert_case($extension, \MB_CASE_LOWER);
            if (\in_array($extension, $invalidExtensions, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $cUrl
     * @return string
     */
    public function normalize(string $cUrl): string
    {
        $url = new URL();
        $url->setUrl($cUrl);

        return '/' . \trim($url->normalize(), '\\/');
    }

    /**
     * @param string $whereSQL
     * @param string $orderSQL
     * @param string $limitSQL
     * @return stdClass[]
     */
    public static function getRedirects(string $whereSQL = '', string $orderSQL = '', string $limitSQL = ''): array
    {
        $redirects = Shop::Container()->getDB()->getObjects(
            'SELECT *
                FROM tredirect' .
            ($whereSQL !== '' ? ' WHERE ' . $whereSQL : '') .
            ($orderSQL !== '' ? ' ORDER BY ' . $orderSQL : '') .
            ($limitSQL !== '' ? ' LIMIT ' . $limitSQL : '')
        );
        foreach ($redirects as $redirect) {
            $redirect->kRedirect            = (int)$redirect->kRedirect;
            $redirect->paramHandling        = (int)$redirect->paramHandling;
            $redirect->nCount               = (int)$redirect->nCount;
            $redirect->cFromUrl             = Text::filterXSS($redirect->cFromUrl);
            $redirect->oRedirectReferer_arr = self::getReferers($redirect->kRedirect);

            foreach ($redirect->oRedirectReferer_arr as $referer) {
                $referer->cRefererUrl = Text::filterXSS($referer->cRefererUrl);
            }
        }

        return $redirects;
    }

    /**
     * @param string $whereSQL
     * @return int
     */
    public static function getRedirectCount(string $whereSQL = ''): int
    {
        return Shop::Container()->getDB()->getSingleInt(
            'SELECT COUNT(kRedirect) AS cnt
                FROM tredirect' .
            ($whereSQL !== '' ? ' WHERE ' . $whereSQL : ''),
            'cnt'
        );
    }

    /**
     * @param int $kRedirect
     * @param int $limit
     * @return stdClass[]
     */
    public static function getReferers(int $kRedirect, int $limit = 100): array
    {
        return Shop::Container()->getDB()->getObjects(
            'SELECT tredirectreferer.*, tbesucherbot.cName AS cBesucherBotName,
                    tbesucherbot.cUserAgent AS cBesucherBotAgent
                FROM tredirectreferer
                LEFT JOIN tbesucherbot
                    ON tredirectreferer.kBesucherBot = tbesucherbot.kBesucherBot
                    WHERE kRedirect = :kr
                ORDER BY dDate ASC
                LIMIT :lmt',
            ['kr' => $kRedirect, 'lmt' => $limit]
        );
    }

    /**
     * @return int
     */
    public static function getTotalRedirectCount(): int
    {
        return Shop::Container()->getDB()->getSingleInt(
            'SELECT COUNT(kRedirect) AS cnt
                FROM tredirect',
            'cnt'
        );
    }

    /**
     * @param string $url - one of
     *                    * full URL (must be inside the same shop) e.g. http://www.shop.com/path/to/page
     *                    * url path e.g. /path/to/page
     *                    * path relative to the shop root url
     * @return bool
     */
    public static function checkAvailability(string $url): bool
    {
        if (empty($url)) {
            return false;
        }
        $parsedUrl     = \parse_url($url);
        $parsedShopUrl = \parse_url(Shop::getURL() . '/');
        if (!\is_array($parsedShopUrl) || !\is_array($parsedUrl)) {
            return false;
        }
        $fullUrlParts = $parsedUrl;
        if (!isset($parsedUrl['host']) && isset($parsedShopUrl['scheme'], $parsedShopUrl['host'])) {
            $fullUrlParts['scheme'] = $parsedShopUrl['scheme'];
            $fullUrlParts['host']   = $parsedShopUrl['host'];
        } elseif (($parsedUrl['host'] ?? '???') !== ($parsedShopUrl['host'] ?? '?')) {
            return false;
        }

        if (!isset($parsedUrl['path'])) {
            $fullUrlParts['path'] = $parsedShopUrl['path'] ?? '';
        } elseif (!\str_starts_with($parsedUrl['path'], $parsedShopUrl['path'] ?? 'invalid')) {
            if (isset($parsedUrl['host'])) {
                return false;
            }
            $fullUrlParts['path'] = ($parsedShopUrl['path'] ?? '') . \ltrim($parsedUrl['path'], '/');
        }

        if (isset($fullUrlParts['query'])) {
            $fullUrlParts['query'] .= '&notrack';
        } else {
            $fullUrlParts['query'] = 'notrack';
        }
        if (!\DEFAULT_CURL_OPT_VERIFYPEER) {
            \stream_context_set_default([
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ],
            ]);
        }
        $headers = \get_headers(URL::unparseURL($fullUrlParts));
        if ($headers !== false) {
            foreach ($headers as $header) {
                if (\preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $header)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param int $kRedirect
     */
    public static function deleteRedirect(int $kRedirect): void
    {
        Shop::Container()->getDB()->delete('tredirect', 'kRedirect', $kRedirect);
        Shop::Container()->getDB()->delete('tredirectreferer', 'kRedirect', $kRedirect);
    }

    /**
     * @return int
     */
    public static function deleteUnassigned(): int
    {
        return Shop::Container()->getDB()->getAffectedRows(
            "DELETE tredirect, tredirectreferer
                FROM tredirect
                LEFT JOIN tredirectreferer
                    ON tredirect.kRedirect = tredirectreferer.kRedirect
                WHERE tredirect.cToUrl = ''"
        );
    }

    /**
     * @param array<string, mixed>|null $hookInfos
     * @param bool                      $forceExit
     * @return array<string, mixed>
     */
    public static function urlNotFoundRedirect(array $hookInfos = null, bool $forceExit = false): array
    {
        $shopSubPath = \parse_url(Shop::getURL(), \PHP_URL_PATH) ?: '';
        $url         = \preg_replace('/^' . \preg_quote($shopSubPath, '/') . '/', '', $_SERVER['REQUEST_URI'] ?? '', 1);
        $redirect    = new self();
        $redirectUrl = $redirect->test($url);
        if ($redirectUrl !== false && $redirectUrl !== $url && '/' . $redirectUrl !== $url) {
            $parsed = \parse_url($redirectUrl);
            if (!isset($parsed['scheme'])) {
                $redirectUrl = \str_starts_with($redirectUrl, '/')
                    ? Shop::getURL() . $redirectUrl
                    : Shop::getURL() . '/' . $redirectUrl;
            }
            \http_response_code(301);
            \header('Location: ' . $redirectUrl);
            exit;
        }
        \http_response_code(404);

        if ($forceExit || !$redirect->isValid($url)) {
            exit;
        }
        $isFileNotFound = true;
        \executeHook(\HOOK_PAGE_NOT_FOUND_PRE_INCLUDE, [
            'isFileNotFound'  => &$isFileNotFound,
            $hookInfos['key'] => &$hookInfos['value']
        ]);
        $hookInfos['isFileNotFound'] = $isFileNotFound;

        return $hookInfos;
    }
}
