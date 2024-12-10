<?php

declare(strict_types=1);

namespace JTL\Router;

use Exception;
use FastRoute\BadRouteException;
use JTL\Cache\JTLCacheInterface;
use JTL\Catalog\Currency;
use JTL\DB\DbInterface;
use JTL\Events\Dispatcher as CoreDispatcher;
use JTL\Events\Event;
use JTL\Language\LanguageHelper;
use JTL\REST\Registrator;
use JTL\Router\Controller\CategoryController;
use JTL\Router\Controller\CharacteristicValueController;
use JTL\Router\Controller\ConsentController;
use JTL\Router\Controller\ControllerInterface;
use JTL\Router\Controller\DefaultController;
use JTL\Router\Controller\FaviconController;
use JTL\Router\Controller\IOController;
use JTL\Router\Controller\ManufacturerController;
use JTL\Router\Controller\MediaImageController;
use JTL\Router\Controller\NewsController;
use JTL\Router\Controller\PageController;
use JTL\Router\Controller\ProductController;
use JTL\Router\Controller\RootController;
use JTL\Router\Controller\SearchController;
use JTL\Router\Controller\SearchQueryController;
use JTL\Router\Controller\SearchSpecialController;
use JTL\Router\Middleware\ApiKeyMiddleware;
use JTL\Router\Middleware\CartcheckMiddleware;
use JTL\Router\Middleware\CurrencyCheckMiddleware;
use JTL\Router\Middleware\LocaleCheckMiddleware;
use JTL\Router\Middleware\LocaleRedirectMiddleware;
use JTL\Router\Middleware\MaintenanceModeMiddleware;
use JTL\Router\Middleware\OptinMiddleware;
use JTL\Router\Middleware\SSLRedirectMiddleware;
use JTL\Router\Middleware\WishlistCheckMiddleware;
use JTL\Router\Strategy\SmartyStrategy;
use JTL\Services\JTL\AlertServiceInterface;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Exception\EmitterException;
use League\Fractal\Manager;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Route;
use League\Route\RouteGroup;
use Psr\Http\Server\MiddlewareInterface;

use function Functional\first;

/**
 * Class Router
 * @package JTL\Router
 * @since 5.2.0
 */
class Router
{
    /**
     * @var string
     */
    private string $uri = '';

    /**
     * @var BaseRouter
     */
    private BaseRouter $router;

    /**
     * @var RouteGroup|null
     */
    private ?RouteGroup $rapi = null;

    /**
     * @var bool
     */
    private bool $isMultilang = false;

    /**
     * @var bool
     */
    private bool $isMulticrncy = false;

    /**
     * @var string[]
     */
    private array $langGroups = [''];

    /**
     * @var string[]
     */
    private array $crncyGroups = ['/'];

    /**
     * @var string
     */
    private string $defaultLocale = 'de';

    /**
     * @var bool
     */
    private bool $isMultiDomain = false;

    /**
     * @var array{host: string, scheme: string, port: int|null,
     *       locale: string, iso: string, id: int, default: bool,
     *       prefix: string, currency: bool, localized: bool}[]
     */
    private array $hosts;

    /**
     * @var string
     */
    private string $path = '';

    /**
     * @var RouteGroup[]
     */
    private array $routes = [];

    /**
     * @var array<array<string, array<int|string, array<string>|string>>>
     */
    private array $customRoutes = [];

    /**
     * @var array<string, string>
     */
    private array $languages = [];

    /**
     * @var ControllerInterface[]
     */
    private array $controllers;

    public const TYPE_CATEGORY             = 'categories';
    public const TYPE_CHARACTERISTIC_VALUE = 'characteristics';
    public const TYPE_MANUFACTURER         = 'manufacturers';
    public const TYPE_NEWS                 = 'news';
    public const TYPE_PAGE                 = 'pages';
    public const TYPE_PRODUCT              = 'products';
    public const TYPE_SEARCH_SPECIAL       = 'searchspecials';
    public const TYPE_SEARCH_QUERY         = 'searchqueries';

    /**
     * @var ControllerInterface
     */
    private ControllerInterface $defaultController;

    /**
     * @param DbInterface                         $db
     * @param JTLCacheInterface                   $cache
     * @param State                               $state
     * @param AlertServiceInterface               $alert
     * @param array<string, array<string, mixed>> $config
     */
    public function __construct(
        protected DbInterface $db,
        protected JTLCacheInterface $cache,
        protected State $state,
        protected AlertServiceInterface $alert,
        private readonly array $config
    ) {
        $this->defaultController = new DefaultController($db, $cache, $state, $this->config, $alert);

        $this->controllers = [
            ProductController::class             => new ProductController($db, $cache, $state, $this->config, $alert),
            CharacteristicValueController::class =>
                new CharacteristicValueController($db, $cache, $state, $this->config, $alert),
            CategoryController::class            => new CategoryController($db, $cache, $state, $this->config, $alert),
            SearchSpecialController::class       =>
                new SearchSpecialController($db, $cache, $state, $this->config, $alert),
            SearchQueryController::class         =>
                new SearchQueryController($db, $cache, $state, $this->config, $alert),
            ManufacturerController::class        =>
                new ManufacturerController($db, $cache, $state, $this->config, $alert),
            NewsController::class                => new NewsController($db, $cache, $state, $this->config, $alert),
            SearchController::class              => new SearchController($db, $cache, $state, $this->config, $alert),
            PageController::class                => new PageController($db, $cache, $state, $this->config, $alert),
            MediaImageController::class          => new MediaImageController(
                $db,
                $cache,
                $state,
                $this->config,
                $alert
            ),
            FaviconController::class             => new FaviconController($db, $cache, $state, $this->config, $alert)
        ];
        $this->prepare();
    }

    public function prepare(): void
    {
        $this->router = new BaseRouter();
        $this->routes = [];

        $registeredDefault = false;
        $middlewares       = [
            new MaintenanceModeMiddleware($this->config['global']),
            new SSLRedirectMiddleware($this->config['global']),
            new WishlistCheckMiddleware(),
            new CartcheckMiddleware(),
            new LocaleCheckMiddleware(),
            new CurrencyCheckMiddleware(),
            new OptinMiddleware(),
        ];
        $root              = new RootController($this->db, $this->cache, $this->state, $this->config, $this->alert);
        $consent           = new ConsentController();
        $io                = new IOController($this->db, $this->cache, $this->state, $this->config, $this->alert);
        foreach ($this->collectHosts() as $data) {
            $host         = $data['host'];
            $locale       = $data['locale'];
            $localePrefix = $data['prefix'];
            $group        = new RouteGroup($localePrefix, function (RouteGroup $route) use (
                &$registeredDefault,
                $data,
                $io,
                $root,
                $consent,
                $locale
            ) {
                $dynName = $this->isMultiDomain === true ? ('_' . $locale) : '';
                if ($data['localized']) {
                    $dynName = '_LOCALIZED';
                }
                if ($data['currency']) {
                    $dynName .= '_CRNCY';
                }
                if (($this->isMultiDomain === true || $registeredDefault === false) && $route->getPrefix() === '/') {
                    // these routes must only be registered once per host
                    $registeredDefault = true;
                    $consent->register($route, $dynName);
                    $io->register($route, $dynName);
                    $root->register($route, $dynName);
                }
                foreach ($this->controllers as $controller) {
                    $controller->register($route, $dynName);
                }
            }, $this->router);
            $group->setHost($host)->setName(
                $locale
                . '_grp'
                . ($data['localized'] ? '_LOCALIZED' : '')
                . ($data['currency'] ? '_CRNCY' : '')
            );
            foreach ($middlewares as $middleware) {
                $group->middleware($middleware);
            }
            $this->routes[] = $group;
        }
        if ($this->isMultiDomain === false) {
            $path = \parse_url(\URL_SHOP, \PHP_URL_PATH);
            if (\is_string($path) && $path !== '/') {
                $this->path = $path;
            }
            $port = \parse_url(\URL_SHOP, \PHP_URL_PORT);
            if (\is_int($port)) {
                $this->router->setPort($port);
            }
        }
        $this->collectGroupRoutes();
    }

    /**
     * @return void
     */
    private function registerAPI(): void
    {
        if (\SHOW_REST_API === false) {
            return;
        }
        $this->rapi = new RouteGroup('/api/v1', function (RouteGroup $group) {
            $registrator = new Registrator(new Manager(), $this->db, $this->cache);
            $registrator->register($group);
        }, $this->router);
        $this->rapi->setName('restapi_grp');
        $this->rapi->middleware(new ApiKeyMiddleware($this->db));
    }

    /**
     * @return void
     */
    protected function collectGroupRoutes(): void
    {
        foreach ($this->routes as $group) {
            $group();
        }
    }

    /**
     * @param string                   $slug
     * @param callable                 $cb
     * @param string|null              $name
     * @param string[]                 $methods
     * @param MiddlewareInterface|null $middleware
     * @return Route[]
     */
    public function addRoute(
        string $slug,
        callable $cb,
        ?string $name = null,
        array $methods = ['GET'],
        ?MiddlewareInterface $middleware = null
    ): array {
        if (!\str_starts_with($slug, '/')) {
            $slug = '/' . $slug;
        }
        $name                      = $name ?? \uniqid('', true);
        $routes                    = [];
        $methods                   = \array_map(static fn($value) => \mb_strtoupper($value), $methods);
        $this->customRoutes[$name] = [];
        foreach ($methods as $method) {
            $this->customRoutes[$name][$method] = [];
        }
        foreach ($this->routes as $group) {
            $groupName = $group->getName();
            if ($groupName === null) {
                continue;
            }
            // routes are named <locale>_grp, <locale>_grp_LOCALIZED, <locale>_grp_CRNCY etc.
            $dynName = $this->isMultiDomain === true ? ('_' . \explode('_', $groupName)[0]) : '';
            if (\str_contains($groupName, '_LOCALIZED')) {
                $dynName = '_LOCALIZED';
            }
            if (\str_contains($groupName, '_CRNCY')) {
                $dynName .= '_CRNCY';
            }
            foreach ($methods as $method) {
                $route = $group->map($method, $slug, $cb);
                $route->setName($name . $dynName . $method);
                $this->customRoutes[$name][$method][] = $name . $dynName . $method;
                if ($middleware !== null) {
                    $route->middleware($middleware);
                }
                $routes[] = $route;
            }
        }

        return $routes;
    }

    /**
     * @return void
     */
    protected function registerDefaultController(): void
    {
        foreach ($this->routes as $group) {
            $groupName = $group->getName();
            $dynName   = '';
            if ($groupName === null) {
                continue;
            }
            if ($this->isMultiDomain === true) {
                $locale  = \mb_substr($groupName, 0, \mb_strpos($groupName, '_grp'));
                $dynName = '_' . $locale;
            }
            if (\str_contains($groupName, '_LOCALIZED')) {
                $dynName = '_LOCALIZED';
            }
            if (\str_contains($groupName, '_CURNCY')) {
                $dynName .= '_CRNCY';
            }
            $this->defaultController->register($group, $dynName);
        }
    }

    /**
     * @param bool $decoded - true to decode %-sequences in the URI, false to leave them unchanged
     * @return string
     */
    public function getRequestUri(bool $decoded = false): string
    {
        /** @var string $shopPath */
        $shopPath = \parse_url(Shop::getURL(), \PHP_URL_PATH) ?? '';
        $basePath = \parse_url($this->getRequestURL(), \PHP_URL_PATH);
        $uri      = $basePath ? \mb_substr($basePath, \mb_strlen($shopPath) + 1) : '';
        $uri      = '/' . $uri;
        if ($decoded) {
            $uri = \rawurldecode($uri);
        }

        return $uri;
    }

    /**
     * @param string                    $type
     * @param array<string, mixed>|null $replacements
     * @param bool                      $byName
     * @return string
     */
    public function getPathByType(string $type, ?array $replacements = null, bool $byName = true): string
    {
        if (isset($replacements['name']) && $replacements['name'] === '') {
            unset($replacements['name']);
        }
        $isDefaultLocale = ($replacements['lang'] ?? '') === $this->defaultLocale;
        if (empty($replacements['lang'])) {
            $replacements['lang'] = $this->defaultLocale;
            $isDefaultLocale      = true;
        }
        $name   = $this->getRouteName($type, $replacements, $byName);
        $scheme = $isDefaultLocale
            ? ($this->config['global']['routing_default_language'] ?? 'F')
            : ($this->config['global']['routing_scheme'] ?? 'F');
        if (\ENABLE_EXPERIMENTAL_ROUTING_SCHEMES === false) {
            $scheme = 'F';
        }
        if ($scheme !== 'F' && $byName === true && empty($replacements['name'])) {
            $byName = false;
        }
        if ($byName === true) {
            if ($scheme === 'F') {
                if (!isset($replacements['name'])) {
                    $param       = $this->getFallbackParam($type);
                    $queryParams = [$param => $replacements['id']];
                    if (!$isDefaultLocale) {
                        $queryParams['lang'] = $this->languages[$replacements['lang']];
                    }
                    if (isset($replacements['currency'])) {
                        $queryParams['curr'] = $replacements['currency'];
                    }
                    $named = '?' . \http_build_query($queryParams);
                } else {
                    $named = $replacements['name'];
                }

                return $this->path . '/' . $named
                    . (isset($replacements['currency']) ? '?curr=' . $replacements['currency'] : '');
            }
            if ($scheme === 'L') {
                return $this->path . '/' . $replacements['lang'] . '/'
                    . ($replacements['name'] ?? $replacements['id'] ?? '')
                    . (isset($replacements['currency']) ? '?curr=' . $replacements['currency'] : '');
            }
        }

        return $this->path . $this->getNamedPath($name, $replacements);
    }

    /**
     * @param string                    $type
     * @param array<string, mixed>|null $replacements
     * @param bool                      $byName
     * @param bool                      $forceDynamic
     * @return string
     */
    public function getURLByType(
        string $type,
        ?array $replacements = null,
        bool $byName = true,
        bool $forceDynamic = false
    ): string {
        if (isset($replacements['name']) && $replacements['name'] === '') {
            unset($replacements['name']);
        }
        $isDefaultLocale = ($replacements['lang'] ?? '') === $this->defaultLocale;
        if (empty($replacements['lang'])) {
            $replacements['lang'] = $this->defaultLocale;
            $isDefaultLocale      = true;
        }
        $name = $this->getRouteName($type, $replacements, $byName);
        try {
            $route = $this->router->getNamedRoute($name);
        } catch (Exception) {
            return '';
        }
        $pfx = $this->getPrefix($route->getHost());
        if ($this->path !== '/') {
            $pfx .= $this->path;
        }
        $scheme = $isDefaultLocale
            ? ($this->config['global']['routing_default_language'] ?? 'F')
            : ($this->config['global']['routing_scheme'] ?? 'F');
        if (\ENABLE_EXPERIMENTAL_ROUTING_SCHEMES === false) {
            $scheme = 'F';
        }
        if ($scheme !== 'F' && $byName === true && empty($replacements['name'])) {
            $byName = false;
        }
        if ($forceDynamic === false && $byName === true) {
            if ($scheme === 'F') {
                if (!isset($replacements['name'])) {
                    $param       = $this->getFallbackParam($type);
                    $queryParams = [$param => $replacements['id']];
                    if (!$isDefaultLocale) {
                        $queryParams['lang'] = $this->languages[$replacements['lang']];
                    }
                    if (isset($replacements['currency'])) {
                        $queryParams['curr'] = $replacements['currency'];
                    }
                    $named = '?' . \http_build_query($queryParams);
                } else {
                    $named = $replacements['name'];
                }

                return $pfx . '/' . $named
                    . (isset($replacements['currency']) ? '?curr=' . $replacements['currency'] : '');
            }
            if ($scheme === 'L') {
                return $pfx . '/' . $replacements['lang'] . '/'
                    . ($replacements['name'] ?? $replacements['id'] ?? '')
                    . (isset($replacements['currency']) ? '?curr=' . $replacements['currency'] : '');
            }
        }

        return $pfx . $this->getPath($route->getPath(), $replacements);
    }

    /**
     * @param string|null $routeHost
     * @return string
     */
    private function getPrefix(?string $routeHost): string
    {
        if ($routeHost === null) {
            return Shop::getURL();
        }
        foreach ($this->hosts as $host) {
            if ($host['host'] !== $routeHost) {
                continue;
            }
            $port = $host['port'] > 0
                ? ':' . $host['port']
                : '';

            return $host['scheme'] . '://' . $routeHost . $port;
        }

        return Shop::getURL();
    }

    /**
     * @param string                    $name
     * @param array<string, mixed>|null $replacements
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getNamedPath(string $name, ?array $replacements = null): string
    {
        try {
            $path = $this->router->getNamedRoute($name)->getPath();
        } catch (Exception) {
            return '';
        }

        return $replacements === null ? $path : $this->getPath($path, $replacements);
    }

    /**
     * @param string                    $type
     * @param array<string, mixed>|null $replacements
     * @param bool                      $byName
     * @return string
     */
    private function getRouteName(string $type, ?array $replacements = null, bool $byName = true): string
    {
        $name = match ($type) {
            self::TYPE_CATEGORY             => 'ROUTE_CATEGORY_BY_',
            self::TYPE_CHARACTERISTIC_VALUE => 'ROUTE_CHARACTERISTIC_BY_',
            self::TYPE_MANUFACTURER         => 'ROUTE_MANUFACTURER_BY_',
            self::TYPE_NEWS                 => 'ROUTE_NEWS_BY_',
            self::TYPE_PAGE                 => 'ROUTE_PAGE_BY_',
            self::TYPE_PRODUCT              => 'ROUTE_PRODUCT_BY_',
            self::TYPE_SEARCH_SPECIAL       => 'ROUTE_SEARCHSPECIAL_BY_',
            self::TYPE_SEARCH_QUERY         => 'ROUTE_SEARCHQUERY_BY_',
            default                         => $type
        };

        $proto        = '';
        $isNamedRoute = $name === $type;
        if ($isNamedRoute === false) {
            $name .= ($byName === true && !empty($replacements['name']) ? 'NAME' : 'ID');
        } elseif (isset($this->customRoutes[$name])) {
            $proto = first(\array_keys($this->customRoutes[$name]));
        }

        if ($this->isMultiDomain === true) {
            $name .= '_' . \mb_convert_case((string)($replacements['lang'] ?? ''), \MB_CASE_LOWER);
        } else {
            $isDefaultLocale = ($replacements['lang'] ?? '') === $this->defaultLocale;
            $defaultScheme   = $this->config['global']['routing_default_language'] ?? 'F';
            $scheme          = $this->config['global']['routing_scheme'] ?? 'F';
            if (\ENABLE_EXPERIMENTAL_ROUTING_SCHEMES === false) {
                $scheme = 'F';
            }
            if (!$isDefaultLocale && ($scheme === 'LP' || $scheme === 'L')) {
                $name .= '_LOCALIZED';
            } elseif ($isDefaultLocale && ($defaultScheme === 'LP' || $defaultScheme === 'L')) {
                $name .= '_LOCALIZED';
            }
        }
        if ($this->isMulticrncy === true && isset($replacements['currency'])) {
            $name .= '_CRNCY';
        }
        if ($isNamedRoute === true) {
            $name .= $proto;
        }

        return $name;
    }

    /**
     * fixed version of League\Route::getPath() with replacements
     *
     * @param string               $path
     * @param array<string, mixed> $replacements
     * @return string
     */
    protected function getPath(string $path, array $replacements): string
    {
        $hasReplacementRegex = '/{(' . \implode('|', \array_keys($replacements)) . ')(:.*)?}/';

        \preg_match_all('/\[(.*?)?{(?<keys>.*?)}/', $path, $matches);

        $quoted = [];
        foreach ($matches['keys'] as $key) {
            $quoted[] = \preg_quote($key, '/');
        }
        $isOptionalRegex = '/(.*)?{(' . \implode('|', $quoted) . ')(:.*)?}(.*)?/';

        $isPartiallyOptionalRegex = '/^([^\[\]{}]+)?\[((?:.*)?{(?:'
            . \implode('|', $matches['keys'])
            . ')(?::.*)?}(?:.*)?)]?([^\[\]{}]+)?(?:[\[\]]+)?$/';

        $toReplace = [];

        foreach ($replacements as $wildcard => $actual) {
            $toReplace['/{' . \preg_quote($wildcard, '/') . '(:[^\}]*)?}/'] = $actual;
        }
        $segments = [];
        /** @var string $segment */
        foreach (\array_filter(\explode('/', $path)) as $segment) {
            // segment is partially optional with a wildcard, strip it if no match, tidy up if match
            if (\preg_match($isPartiallyOptionalRegex, $segment)) {
                /** @var string $segment */
                $segment = \preg_match($hasReplacementRegex, $segment)
                    ? \preg_replace($isPartiallyOptionalRegex, '$1$2$3', $segment)
                    : \preg_replace($isPartiallyOptionalRegex, '$1', $segment);
            }
            // segment either isn't a wildcard or there is a replacement
            $c0 = !\preg_match('/{(.*?)}/', $segment);
            $c1 = \preg_match($hasReplacementRegex, $segment);
            if ($c0 || $c1) {
                $item       = \preg_replace(['/\[$/', '/]+$/'], '', $segment);
                $segments[] = $item;
                continue;
            }
            // segment is a required wildcard, no replacement, still gets added
            if (!\preg_match($isOptionalRegex, $segment)) {
                $item       = \preg_replace(['/\[$/', '/]+$/'], '', $segment);
                $segments[] = $item;
                continue;
            }
            // segment is completely optional with no replacement, strip it and break
            if (\preg_match($isOptionalRegex, $segment) && !\preg_match($hasReplacementRegex, $segment)) {
                break;
            }
        }

        return \preg_replace(\array_keys($toReplace), \array_values($toReplace), '/' . \implode('/', $segments)) ?? '';
    }

    /**
     * @return string
     */
    public function getRequestURL(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
            . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['HTTP_X_REWRITE_URL'] ?? $_SERVER['REQUEST_URI'] ?? '');
    }

    public function dispatch(JTLSmarty $smarty): never
    {
        $strategy = new SmartyStrategy(new ResponseFactory(), $smarty, $this->state);
        $this->router->setStrategy($strategy);
        $body          = $this->getPostBodyJsonData();
        $request       = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $body, $_COOKIE, $_FILES);
        $requestedHost = $request->getUri()->getHost();
        $isValidHost   = false;
        foreach ($this->hosts as $host) {
            if ($host['host'] !== $requestedHost) {
                continue;
            }
            $isValidHost = true;
            if (\EXPERIMENTAL_MULTILANG_SHOP === true || Shop::$forceHost[0]['host'] !== '') {
                $this->state->languageID = $host['id'];
                Shop::updateLanguage($this->state->languageID, $host['iso']);
            }
        }
        /** @var string|null $shopPath */
        $shopPath = \parse_url(Shop::getURL(), \PHP_URL_PATH);
        $uri      = $request->getUri();
        if ($shopPath !== null) {
            /** @var string $basePath */
            $basePath = \parse_url($this->getRequestURL(), \PHP_URL_PATH) ?? '';
            $path     = '/' . \mb_substr($basePath, \mb_strlen($shopPath) + 1);
            $request  = $request->withUri($uri->withPath($path));
        }
        $uriPath = $request->getUri()->getPath();
        $oldURI  = $uriPath;
        \executeHook(\HOOK_SEOCHECK_ANFANG, ['uri' => &$uriPath]);
        if ($oldURI !== $uriPath) {
            $request = $request->withUri($uri->withPath($uriPath));
        }
        $this->registerAPI();
        \executeHook(\HOOK_ROUTER_PRE_DISPATCH, ['router' => $this]);
        if ($this->rapi !== null) {
            $rapi = $this->rapi;
            $rapi();
        }
        // this is added after HOOK_ROUTER_PRE_DISPATCH since plugins could register static routes
        // which would otherwise be shadowed by this
        $this->registerDefaultController();
        try {
            $response = $this->router->dispatch($request);
        } catch (BadRouteException $e) {
            throw $e;
        } catch (NotFoundException) {
            if ($isValidHost === true) {
                $response = $this->defaultController->getResponse($request, [], $smarty);
            } else {
                $targetURI = $request->getUri()->withHost($this->hosts[0]['host']);
                Shop::Container()->getLogService()->warning(
                    'Invalid host requested: {host} - redirecting to {url}',
                    ['host' => $requestedHost, 'url' => (string)$targetURI]
                );
                $response = new RedirectResponse($targetURI);
            }
        } catch (Exception $e) {
            Shop::Container()->getLogService()->error('Routing error: {err}', ['err' => $e->getMessage()]);
            $response = $this->defaultController->getResponse($request, [], $smarty);
        }
        CoreDispatcher::getInstance()->fire(Event::EMIT);
        try {
            (new SapiEmitter())->emit($response);
        } catch (EmitterException) {
            echo $response->getBody();
        }
        exit;
    }

    /**
     * @return State
     */
    public function init(): State
    {
        $this->state->initFromRequest();

        return $this->state;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        $params = [];
        foreach ($this->state->getMapping() as $old => $new) {
            $params[$old] = $this->state->{$new};
        }

        return $params;
    }

    /**
     * @return array{host: string, scheme: string, port: int|null,
     *      locale: string, iso: string, id: int, default: bool,
     *      prefix: string, currency: bool, localized: bool}[]
     */
    private function collectHosts(): array
    {
        $hosts     = [];
        $locales   = [];
        $forceHost = false;
        if (Shop::$forceHost[0]['host'] !== '') {
            $this->isMultiDomain = true;
            foreach (Shop::$forceHost as $hostData) {
                $hosts[] = [
                    'host'      => $hostData['host'],
                    'scheme'    => $hostData['scheme'],
                    'port'      => $hostData['port'] ?? null,
                    'locale'    => $hostData['locale'],
                    'iso'       => $hostData['iso'],
                    'id'        => $hostData['id'],
                    'default'   => true,
                    'prefix'    => '/',
                    'currency'  => false,
                    'localized' => false
                ];
                if ($hostData['host'] === $_SERVER['HTTP_HOST']) {
                    $this->defaultLocale = $hostData['locale'];
                    $forceHost           = true;
                }
            }
        }
        if (!$forceHost) {
            foreach (LanguageHelper::getAllLanguages() as $language) {
                $default   = $language->isShopDefault();
                $code      = $language->getCode();
                $locales[] = $language->getIso639();

                $this->languages[$language->getIso639()] = $code;
                if (\EXPERIMENTAL_MULTILANG_SHOP === false && $default) {
                    $url     = \URL_SHOP;
                    $host    = \parse_url($url);
                    $hosts[] = [
                        'host'      => $host['host'] ?? '',
                        'scheme'    => $host['scheme'] ?? 'http',
                        'port'      => $host['port'] ?? null,
                        'locale'    => $language->getIso639(),
                        'iso'       => $code,
                        'id'        => $language->getId(),
                        'default'   => true,
                        'prefix'    => '/',
                        'currency'  => false,
                        'localized' => false
                    ];
                } elseif (\defined('URL_SHOP_' . \mb_convert_case($code, \MB_CASE_UPPER))) {
                    $this->isMultiDomain = true;
                    /** @var string $url */
                    $url     = \constant('URL_SHOP_' . \mb_convert_case($code, \MB_CASE_UPPER));
                    $host    = \parse_url($url);
                    $hosts[] = [
                        'host'      => $host['host'] ?? '',
                        'scheme'    => $host['scheme'] ?? 'http',
                        'port'      => $host['port'] ?? null,
                        'locale'    => $language->getIso639(),
                        'iso'       => $code,
                        'id'        => $language->getId(),
                        'default'   => $default,
                        'prefix'    => '/',
                        'currency'  => false,
                        'localized' => false
                    ];
                }
                if ($default) {
                    $this->defaultLocale = $language->getIso639();
                }
            }
        }
        $defaultScheme = $this->config['global']['routing_default_language'] ?? 'F';
        $otherSchemes  = $this->config['global']['routing_scheme'] ?? 'F';
        if (\ENABLE_EXPERIMENTAL_ROUTING_SCHEMES === false) {
            $defaultScheme = 'F';
            $otherSchemes  = 'F';
        }
        if ($defaultScheme !== 'F' || $otherSchemes !== 'F') {
            if ($this->isMultiDomain === false && \count($locales) > 1) {
                $host2              = $hosts[0];
                $this->isMultilang  = true;
                $host2['prefix']    = '/{lang:(?:' . \implode('|', $locales) . ')}';
                $host2['localized'] = true;
                $hosts[]            = $host2;
            }
        } else {
            $this->router->middleware(new LocaleRedirectMiddleware($this->defaultLocale));
        }
        $currencies = \array_map(static function (Currency $e): string {
            return $e->getCode();
        }, Currency::loadAll());
        if (\count($currencies) > 1) {
            $currencyRegex       = '/{currency:(?:' . \implode('|', $currencies) . ')}';
            $this->crncyGroups[] = $currencyRegex;
            $this->isMulticrncy  = true;
            foreach ($hosts as $host) {
                $base             = $host;
                $base['prefix']   = \rtrim($currencyRegex . $base['prefix'], '/');
                $base['currency'] = true;
                $hosts[]          = $base;
            }
        }
        $this->hosts = \array_reverse($hosts);

        return $this->hosts;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getFallbackParam(string $type): string
    {
        return match ($type) {
            self::TYPE_CATEGORY             => \QUERY_PARAM_CATEGORY,
            self::TYPE_CHARACTERISTIC_VALUE => \QUERY_PARAM_CHARACTERISTIC_VALUE,
            self::TYPE_MANUFACTURER         => \QUERY_PARAM_MANUFACTURER,
            self::TYPE_NEWS                 => \QUERY_PARAM_NEWS_ITEM,
            self::TYPE_PAGE                 => \QUERY_PARAM_LINK,
            self::TYPE_PRODUCT              => \QUERY_PARAM_PRODUCT,
            self::TYPE_SEARCH_SPECIAL       => \QUERY_PARAM_SEARCH_SPECIAL,
            self::TYPE_SEARCH_QUERY         => \QUERY_PARAM_SEARCH_QUERY_ID,
            default                         => 'unknown'
        };
    }

    /**
     * @return ControllerInterface[]
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * @param string $class
     * @return ControllerInterface|null
     */
    public function getControllerByClassName(string $class): ?ControllerInterface
    {
        return $this->controllers[$class] ?? null;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @return BaseRouter
     */
    public function getRouter(): BaseRouter
    {
        return $this->router;
    }

    /**
     * @param BaseRouter $router
     */
    public function setRouter(BaseRouter $router): void
    {
        $this->router = $router;
    }

    /**
     * @return string[]
     */
    public function getLangGroups(): array
    {
        return $this->langGroups;
    }

    /**
     * @param string[] $langGroups
     */
    public function setLangGroups(array $langGroups): void
    {
        $this->langGroups = $langGroups;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState(State $state): void
    {
        $this->state = $state;
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     * @throws \JsonException
     */
    private function dismissStdClasses(array $body): array
    {
        return \json_decode(\json_encode($body, \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>
     * @throws \JsonException
     */
    private function getPostBodyJsonData(): array
    {
        $body   = $_POST;
        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        $isJSON = $this->checkContentType('application/json');
        if (($method === 'PUT' || $method === 'POST') && $isJSON === true) {
            $tmp = \file_get_contents('php://input');
            if ($tmp !== '' && $tmp !== false) {
                $body = (array)\json_decode(
                    $tmp,
                    null,
                    512,
                    \JSON_THROW_ON_ERROR
                );
                $body = $this->dismissStdClasses($body);
            } else {
                $body = [];
            }
        }

        return $body;
    }

    /**
     * @param string $type
     * @return bool
     */
    private function checkContentType(string $type): bool
    {
        $identifiers = ['HTTP_CONTENT_TYPE', 'CONTENT_TYPE'];
        foreach ($identifiers as $identifier) {
            if (isset($_SERVER[$identifier])) {
                return $_SERVER[$identifier] === $type;
            }
        }

        return false;
    }
}
