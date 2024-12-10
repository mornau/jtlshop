<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use Illuminate\Support\Collection;
use JTL\Backend\Menu;
use JTL\Backend\Permissions;
use JTL\Backend\Settings\Manager as SettingsManager;
use JTL\Backend\Settings\Search;
use JTL\Backend\Settings\Sections\SectionInterface;
use JTL\Helpers\Text;
use JTL\Plugin\Admin\Listing;
use JTL\Plugin\Admin\ListingItem;
use JTL\Plugin\Admin\Validation\LegacyPluginValidator;
use JTL\Plugin\Admin\Validation\PluginValidator;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use JTL\Template\XMLReader;
use JTL\XMLParser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class SearchController
 * @package JTL\Router\Controller\Backend
 */
class SearchController extends AbstractBackendController
{
    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::SETTINGS_SEARCH_VIEW);
        $query = $_GET['cSuche'] ?? '';

        $this->adminSearch(\trim($query), true);

        return $smarty->assign('route', $this->route)
            ->getResponse('suche.tpl');
    }

    /**
     * Search for backend settings
     *
     * @param string $query - search string
     * @param bool   $standalonePage - render as standalone page
     * @return string|null
     * @usedby IO!
     */
    public function adminSearch(string $query, bool $standalonePage = false): ?string
    {
        $xmlParser      = new XMLParser();
        $pluginListing  = new Listing(
            $this->db,
            $this->cache,
            new LegacyPluginValidator($this->db, $xmlParser),
            new PluginValidator($this->db, $xmlParser)
        );
        $adminMenuItems = $this->adminMenuSearch($query);
        $settings       = $this->configSearch($query);
        $shippings      = $this->getShippingByName($query);
        $paymentMethods = $this->getPaymentMethodsByName($query);
        $plugins        = $this->searchPlugins($pluginListing, $query);
        $tplSettings    = $this->getTplSettings($query);

        foreach ($shippings as $shipping) {
            $shipping->cName = $this->highlightSearchTerm($shipping->cName, $query);
        }
        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethod->cName = $this->highlightSearchTerm($paymentMethod->cName, $query);
        }
        foreach ($settings as $section) {
            foreach ($section->getSubsections() as $subsection) {
                if ($subsection->show() === true) {
                    $subsection->setHighlightedName(
                        $this->highlightSearchTerm($subsection->getName(), $query)
                    );
                    foreach ($subsection->getItems() as $setting) {
                        $setting->setHighlightedName(
                            $this->highlightSearchTerm($setting->getName(), $query)
                        );
                    }
                }
            }
        }
        $this->smarty->assign('standalonePage', $standalonePage)
            ->assign('query', Text::filterXSS($query))
            ->assign('adminMenuItems', $adminMenuItems)
            ->assign('settings', $settings)
            ->assign('shippings', \count($shippings) > 0 ? $shippings : null)
            ->assign('paymentMethods', \count($paymentMethods) > 0 ? $paymentMethods : null)
            ->assign('plugins', $plugins)
            ->assign('tplSettings', $tplSettings);

        if ($standalonePage) {
            return null;
        }

        return $this->getSmarty()->fetch('suche.tpl');
    }

    /**
     * @param string $query
     * @return SectionInterface[]
     */
    private function configSearch(string $query): array
    {
        $manager = new SettingsManager(
            $this->db,
            $this->getSmarty(),
            $this->account,
            $this->getText,
            $this->alertService
        );

        return (new Search($this->db, $this->getText, $manager))->getResultSections($query);
    }

    /**
     * @param string $query
     * @return array<int, object{title: string, path: string, link: string, icon: string}>
     */
    private function adminMenuSearch(string $query): array
    {
        $results = [];
        foreach ((new Menu($this->db, $this->account, $this->getText))->getStructure() as $menuName => $menu) {
            foreach ($menu->items as $subMenuName => $subMenu) {
                if (\is_array($subMenu)) {
                    foreach ($subMenu as $itemName => $item) {
                        if (
                            \is_object($item)
                            && (\stripos($itemName, $query) !== false
                                || \stripos($subMenuName, $query) !== false
                                || \stripos($menuName, $query) !== false
                            )
                        ) {
                            $name      = $itemName;
                            $path      = $menuName . ' > ' . $subMenuName . ' > ' . $name;
                            $path      = $this->highlightSearchTerm($path, $query);
                            $results[] = (object)[
                                'title' => $itemName,
                                'path'  => $path,
                                'link'  => $item->link,
                                'icon'  => $menu->icon
                            ];
                        }
                    }
                } elseif (
                    \is_object($subMenu)
                    && (\stripos($subMenuName, $query) !== false || \stripos($menuName, $query) !== false)
                ) {
                    $results[] = (object)[
                        'title' => $subMenuName,
                        'path'  => $this->highlightSearchTerm($menuName . ' > ' . $subMenuName, $query),
                        'link'  => $subMenu->link,
                        'icon'  => $menu->icon
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return string
     */
    private function highlightSearchTerm(string $haystack, string $needle): string
    {
        return \preg_replace(
            '/\p{L}*?' . \preg_quote($needle, '/') . '\p{L}*/ui',
            '<mark>$0</mark>',
            $haystack
        ) ?? '';
    }

    /**
     * @param Listing $pluginListing
     * @param string  $query
     * @return Collection<ListingItem>
     */
    private function searchPlugins(Listing $pluginListing, string $query): Collection
    {
        $results = [];

        foreach ($pluginListing->getInstalled() as $item) {
            /** @var ListingItem $item */
            $matches = false;
            $result  = (object)[
                'id'              => $item->getID(),
                'name'            => $item->getName(),
                'highlightedName' => $this->highlightSearchTerm($item->getName(), $query),
            ];

            if (\stripos($item->getName(), $query) !== false) {
                $matches = true;
            }

            $plugin                  = $item->getPlugin();
            $options                 = $plugin->getConfig()->getOptions();
            $result->matchingOptions = $options->filter(function ($option) use ($query, $plugin): bool {
                if ($option->confType !== 'Y') {
                    return false;
                }
                $poKey      = $option->valueID . '_name';
                $translated = \__($poKey);
                if ($translated === $poKey) {
                    $translated = \__($option->niceName);
                }
                if (\stripos($translated, $query) !== false) {
                    $option->name = $this->highlightSearchTerm($translated, $query);
                    $option->url  = $plugin->getPaths()->getBackendURL() . '#plugin-tab-' . $option->menuID;
                    return true;
                }
                return false;
            });

            if ($matches || $result->matchingOptions->isNotEmpty()) {
                $results[] = $result;
            }
        }

        return new Collection($results);
    }

    /**
     * @param string $query
     * @return array
     * @throws \Exception
     */
    private function getTplSettings(string $query): array
    {
        $templateService = Shop::Container()->getTemplateService();
        $template        = $templateService->getActiveTemplate();
        $xmlReader       = new XMLReader();
        $config          = $template->getConfig()->getConfigXML($xmlReader);
        $results         = [];
        $this->getText->loadTemplateLocale('base', $template);

        foreach ($config as $section) {
            foreach ($section->settings as $setting) {
                $name = \__($setting->name);
                if (\stripos($name, $query) !== false) {
                    $results[] = (object)[
                        'name' => $this->highlightSearchTerm($name, $query),
                        'key'  => $setting->key,
                        'dir'  => $template->getDir(),
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * @param string $query
     * @return array<int, \stdClass>
     * @former getShippingByName()
     */
    private function getShippingByName(string $query): array
    {
        $results = [];
        foreach (\explode(',', $query) as $search) {
            $search = \trim($search);
            if (\mb_strlen($search) < 3) {
                continue;
            }
            $hits = $this->db->getObjects(
                'SELECT va.kVersandart, va.cName
                    FROM tversandart AS va
                    LEFT JOIN tversandartsprache AS vs 
                        ON vs.kVersandart = va.kVersandart
                        AND vs.cName LIKE :search
                    WHERE va.cName LIKE :search
                    OR vs.cName LIKE :search',
                ['search' => '%' . $search . '%']
            );
            foreach ($hits as $item) {
                $item->kVersandart           = (int)$item->kVersandart;
                $results[$item->kVersandart] = $item;
            }
        }

        return $results;
    }

    /**
     * @param string $query
     * @return array<int, \stdClass>
     */
    private function getPaymentMethodsByName(string $query): array
    {
        $paymentMethodsByName = [];
        foreach (\explode(',', $query) as $string) {
            $string = \trim($string);
            if (\mb_strlen($string) < 3) {
                continue;
            }
            $data = $this->db->getObjects(
                'SELECT za.kZahlungsart, za.cName
                    FROM tzahlungsart AS za
                    LEFT JOIN tzahlungsartsprache AS zs 
                        ON zs.kZahlungsart = za.kZahlungsart
                        AND zs.cName LIKE :search
                    WHERE za.cName LIKE :search 
                    OR zs.cName LIKE :search
                    GROUP BY za.kZahlungsart',
                ['search' => '%' . $string . '%']
            );
            foreach ($data as $item) {
                $item->kZahlungsart = (int)$item->kZahlungsart;

                $paymentMethodsByName[$item->kZahlungsart] = $item;
            }
        }

        return $paymentMethodsByName;
    }
}
