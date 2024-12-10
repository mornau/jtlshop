<?php

declare(strict_types=1);

namespace JTL\Router\Middleware;

use JTL\Backend\AdminAccount;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Helpers\Form;
use JTL\License\Checker;
use JTL\License\Manager;
use JTL\License\Mapper;
use JTL\Plugin\Admin\StateChanger;
use JTL\Session\Backend;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class LicenseCheckMiddleware
 * @package JTL\Router\Middleware
 */
class LicenseCheckMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly DbInterface $db,
        private readonly JTLCacheInterface $cache,
        private readonly JTLSmarty $smarty,
        private readonly AdminAccount $account
    ) {
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $expired = \collect();
        if (!$this->account->logged()) {
            return $handler->handle($request);
        }
        if (($request->getQueryParams()['licensenoticeaccepted'] ?? null) === 'true') {
            Backend::set('licensenoticeaccepted', 0);
        }
        $this->checkDisablePlugins($request);
        $mapper         = new Mapper(new Manager($this->db, $this->cache));
        $checker        = new Checker(Shop::Container()->getBackendLogService(), $this->db, $this->cache);
        $updates        = $checker->getUpdates($mapper);
        $noticeAccepted = Backend::get('licensenoticeaccepted') ?? -1;
        if ($noticeAccepted === -1 && \SAFE_MODE === false) {
            $expired = $checker->getLicenseViolations($mapper);
        } else {
            $noticeAccepted++;
        }
        if ($noticeAccepted > 5) {
            $noticeAccepted = -1;
        }
        Backend::set('licensenoticeaccepted', $noticeAccepted);
        $this->smarty->assign('licenseItemUpdates', $updates)
            ->assign('expiredLicenses', $expired);

        return $handler->handle($request);
    }

    private function checkDisablePlugins(ServerRequestInterface $request): void
    {
        if (
            $request->getMethod() !== 'POST'
            || ($request->getParsedBody()['action'] ?? null) !== 'disable-expired-plugins'
            || !Form::validateToken()
        ) {
            return;
        }
        $sc = new StateChanger($this->db, $this->cache);
        foreach ($request->getParsedBody()['pluginID'] ?? [] as $pluginID) {
            $sc->deactivate((int)$pluginID);
        }
    }
}
