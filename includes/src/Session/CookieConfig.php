<?php

declare(strict_types=1);

namespace JTL\Session;

use JTL\Language\LanguageHelper;
use JTL\Settings\Option\Globals;
use JTL\Settings\Settings;

/**
 * Class CookieConfig
 * @package JTL\Session
 */
class CookieConfig
{
    /**
     * @var string
     */
    private string $path = '';

    /**
     * @var string
     */
    private string $domain = '';

    /**
     * @var string
     */
    private string $sameSite = '';

    /**
     * @var int
     */
    private int $lifetime = 0;

    /**
     * @var bool
     */
    private bool $httpOnly = false;

    /**
     * @var bool
     */
    private bool $secure = false;

    /**
     * CookieConfig constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->readDefaults();
        $this->mergeWithConfig($settings);
    }

    /**
     *
     */
    private function readDefaults(): void
    {
        $defaults       = \session_get_cookie_params();
        $this->lifetime = $defaults['lifetime'];
        $this->path     = $defaults['path'];
        $this->domain   = $defaults['domain'];
        $this->secure   = $defaults['secure'];
        $this->httpOnly = $defaults['httponly'];
        $this->sameSite = $defaults['samesite'];
    }

    /**
     * @param Settings $settings
     */
    private function mergeWithConfig(Settings $settings): void
    {
        $this->secure   = $this->secure || $settings->bool(Globals::COOKIE_SECURE);
        $this->httpOnly = $this->httpOnly || $settings->bool(Globals::COOKIE_HTTPONLY);
        if (($samesite = $settings->string(Globals::COOKIE_SAMESITE)) !== 'S') {
            $this->sameSite = $samesite;
            if ($this->sameSite === 'N') {
                $this->sameSite = '';
            }
        }
        if (($domain = $settings->string(Globals::COOKIE_DOMAIN)) !== '') {
            $this->domain = $this->experimentalMultiLangDomain($domain);
        }
        if (($lifetime = $settings->int(Globals::COOKIE_LIFETIME)) > 0) {
            $this->lifetime = $lifetime;
        }
        $path = $settings->string(Globals::COOKIE_PATH);
        if (!empty($path)) {
            $this->path = $path;
        }
        $this->secure = $this->secure
            && ($settings->string(Globals::CHECKOUT_SSL) === 'P' || \str_starts_with(\URL_SHOP, 'https://'));
    }

    /**
     * @param string $domain
     * @return string
     */
    private function experimentalMultiLangDomain(string $domain): string
    {
        if (\EXPERIMENTAL_MULTILANG_SHOP !== true) {
            return $domain;
        }
        $host = $_SERVER['HTTP_HOST'] ?? ' ';
        foreach (LanguageHelper::getAllLanguages() as $language) {
            $code = \mb_convert_case($language->getCode(), \MB_CASE_UPPER);
            if (!\defined('URL_SHOP_' . $code)) {
                continue;
            }
            /** @var string $localized */
            $localized = \constant('URL_SHOP_' . $code);
            if (\defined('COOKIE_DOMAIN_' . $code) && \str_contains($localized, $host)) {
                /** @var string $defined */
                $defined = \constant('COOKIE_DOMAIN_' . $code);

                return $defined;
            }
        }

        return $domain;
    }

    /**
     * @return array{use_cookies: string, cookie_domain: string, cookie_secure: bool,
     *      cookie_lifetime: int, cookie_path: string, cookie_httponly: bool,
     *      cookie_samesite: 'Lax'|'lax'|'None'|'none'|'Strict'|'strict'}
     */
    public function getSessionConfigArray(): array
    {
        return [
            'use_cookies'     => '1',
            'cookie_domain'   => $this->getDomain(),
            'cookie_secure'   => $this->isSecure(),
            'cookie_lifetime' => $this->getLifetime(),
            'cookie_path'     => $this->getPath(),
            'cookie_httponly' => $this->isHttpOnly(),
            'cookie_samesite' => $this->getSameSite()
        ];
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @return ('Lax'|'lax'|'None'|'none'|'Strict'|'strict')
     */
    public function getSameSite(): string
    {
        return $this->sameSite;
    }

    /**
     * @param ('Lax'|'lax'|'None'|'none'|'Strict'|'strict') $sameSite
     */
    public function setSameSite(string $sameSite): void
    {
        $this->sameSite = $sameSite;
    }

    /**
     * @return int
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    /**
     * @param int $lifetime
     */
    public function setLifetime(int $lifetime): void
    {
        $this->lifetime = $lifetime;
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @param bool $httpOnly
     */
    public function setHttpOnly(bool $httpOnly): void
    {
        $this->httpOnly = $httpOnly;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @param bool $secure
     */
    public function setSecure(bool $secure): void
    {
        $this->secure = $secure;
    }
}
