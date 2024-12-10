<?php

declare(strict_types=1);

namespace JTL\Router;

use JTL\Backend\AdminAccount;
use JTL\Backend\Menu;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Exceptions\PermissionException;
use JTL\L10n\GetText;
use JTL\Router\Controller\Backend\CodeController;
use JTL\Router\Controller\Backend\Collection;
use JTL\Router\Controller\Backend\DashboardController;
use JTL\Router\Controller\Backend\PasswordController;
use JTL\Router\Controller\Backend\ReportViewController;
use JTL\Router\Middleware\AuthMiddleware;
use JTL\Router\Middleware\LicenseCheckMiddleware;
use JTL\Router\Middleware\RevisionMiddleware;
use JTL\Router\Middleware\UpdateCheckMiddleware;
use JTL\Router\Middleware\WizardCheckMiddleware;
use JTL\Router\Strategy\SmartyStrategy;
use JTL\Services\JTL\AlertServiceInterface;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Exception\EmitterException;
use League\Container\Container;
use League\Route\Http\Exception\NotFoundException;
use League\Route\RouteGroup;
use League\Route\Router;

/**
 * Class BackendRouter
 * @package JTL\Router
 */
class BackendRouter
{
    private Router $router;

    public function __construct(
        protected DbInterface $db,
        protected JTLCacheInterface $cache,
        protected AdminAccount $account,
        protected AlertServiceInterface $alertService,
        protected GetText $getText,
        protected JTLSmarty $smarty
    ) {
        $this->router = new Router();
        $strategy     = new SmartyStrategy(new ResponseFactory(), $this->smarty, new State());
        $container    = new Container();
        $controllers  = (new Collection())->getRoutes();
        foreach ($controllers as $route => $controller) {
            $container->add($controller, function () use ($controller, $route) {
                $controller = new $controller(
                    $this->db,
                    $this->cache,
                    $this->alertService,
                    $this->account,
                    $this->getText
                );
                $controller->setRoute('/' . $route);

                return $controller;
            });
        }
        $strategy->setContainer($container);
        $this->router->setStrategy($strategy);
        $updateCheckMiddleWare  = new UpdateCheckMiddleware($this->db, $this->account);
        $licenseCheckMiddleWare = new LicenseCheckMiddleware($this->db, $this->cache, $this->smarty, $this->account);

        $basePath = (\parse_url(\URL_SHOP, \PHP_URL_PATH) ?? '') . '/' . \PFAD_ADMIN;
        $this->router->group(\rtrim($basePath, '/'), function (RouteGroup $route) use ($controllers) {
            $revisionMiddleware = new RevisionMiddleware($this->db);
            foreach ($controllers as $slug => $controller) {
                if (\in_array($slug, [Route::PASS, Route::DASHBOARD, Route::CODE, Route::REPORT_VIEW], true)) {
                    continue;
                }
                $route->get('/' . $slug, $controller . '::getResponse')->setName($slug);
                $route->post('/' . $slug, $controller . '::getResponse')
                    ->setName($slug . 'POST')
                    ->middleware($revisionMiddleware);
            }
        })->middleware(new AuthMiddleware($account))
            ->middleware($updateCheckMiddleWare)
            ->middleware($licenseCheckMiddleWare)
            ->middleware(new WizardCheckMiddleware($this->db));
        $this->router->get($basePath . Route::PASS, PasswordController::class . '::getResponse')
            ->setName(Route::PASS);
        $this->router->post($basePath . Route::PASS, PasswordController::class . '::getResponse')
            ->setName(Route::PASS . 'POST');
        $this->router->get(
            $basePath . Route::REPORT_VIEW . '/{id}[/{extension:html|json}]',
            ReportViewController::class . '::getResponse'
        )->setName(Route::REPORT_VIEW);
        $this->router->get($basePath . Route::CODE . '/{redir}', CodeController::class . '::getResponse')
            ->setName(Route::CODE);
        $this->router->post($basePath . Route::CODE . '/{redir}', CodeController::class . '::getResponse')
            ->setName(Route::CODE . 'POST');

        $this->router->get($basePath . 'index.php', DashboardController::class . '::getResponse')
            ->setName(Route::DASHBOARD . 'php')
            ->middleware($updateCheckMiddleWare)
            ->middleware($licenseCheckMiddleWare);
        $this->router->get($basePath, DashboardController::class . '::getResponse')
            ->setName(Route::DASHBOARD)
            ->middleware($updateCheckMiddleWare)
            ->middleware($licenseCheckMiddleWare)
            ->middleware(new WizardCheckMiddleware($this->db));
        $this->router->post($basePath, DashboardController::class . '::getResponse')
            ->setName(Route::DASHBOARD . 'POST')
            ->middleware($updateCheckMiddleWare)
            ->middleware($licenseCheckMiddleWare);
        $this->router->post($basePath . 'index.php', DashboardController::class . '::getResponse')
            ->setName(Route::DASHBOARD . 'POSTphp')
            ->middleware($updateCheckMiddleWare)
            ->middleware($licenseCheckMiddleWare);
    }

    public function dispatch(): never
    {
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $menu    = new Menu($this->db, $this->account, $this->getText);
        $data    = $menu->build($request);
        $this->smarty->assign('oLinkOberGruppe_arr', $data);
        try {
            $response = $this->router->dispatch($request);
        } catch (NotFoundException) {
            $response = (new Response())->withStatus(404);
        } catch (PermissionException) {
            $response = $this->smarty->getResponse('tpl_inc/berechtigung.tpl');
        }
        try {
            (new SapiEmitter())->emit($response);
        } catch (EmitterException) {
            echo $response->getBody();
        }
        exit;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
