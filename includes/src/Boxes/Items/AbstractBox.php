<?php

declare(strict_types=1);

namespace JTL\Boxes\Items;

use JTL\Boxes\Renderer\DefaultRenderer;
use JTL\Boxes\Type;
use JTL\Catalog\Product\Artikel;
use JTL\Catalog\Product\ArtikelListe;
use JTL\Helpers\GeneralObject;
use JTL\MagicCompatibilityTrait;
use JTL\Plugin\PluginInterface;
use JTL\Shop;

use function Functional\false;
use function Functional\first;

/**
 * Class AbstractBox
 * @package JTL\Boxes\Items
 */
abstract class AbstractBox implements BoxInterface
{
    use MagicCompatibilityTrait;

    /**
     * @var array<string, string>
     */
    protected static array $mapping = [
        'compatName'     => 'Name',
        'cName'          => 'Name',
        'anzeigen'       => 'ShowCompat',
        'kBox'           => 'ID',
        'kBoxvorlage'    => 'BaseType',
        'nSort'          => 'Sort',
        'eTyp'           => 'Type',
        'cTitel'         => 'Title',
        'cInhalt'        => 'Content',
        'nAnzeigen'      => 'ItemCount',
        'cURL'           => 'URL',
        'Artikel'        => 'Products',
        'oArtikel_arr'   => 'Products',
        'oContainer_arr' => 'Children',
        'bContainer'     => 'ContainerCheckCompat',
        'bAktiv'         => 'IsActive',
        'kContainer'     => 'ContainerID',
        'cFilter'        => 'Filter',
        'oPlugin'        => 'Plugin',
    ];

    /**
     * @var int[]
     */
    private static array $validPageTypes = [
        \PAGE_UNBEKANNT,
        \PAGE_ARTIKEL,
        \PAGE_ARTIKELLISTE,
        \PAGE_WARENKORB,
        \PAGE_MEINKONTO,
        \PAGE_KONTAKT,
        \PAGE_NEWS,
        \PAGE_NEWSLETTER,
        \PAGE_LOGIN,
        \PAGE_REGISTRIERUNG,
        \PAGE_BESTELLVORGANG,
        \PAGE_BEWERTUNG,
        \PAGE_PASSWORTVERGESSEN,
        \PAGE_WARTUNG,
        \PAGE_WUNSCHLISTE,
        \PAGE_VERGLEICHSLISTE,
        \PAGE_STARTSEITE,
        \PAGE_VERSAND,
        \PAGE_AGB,
        \PAGE_DATENSCHUTZ,
        \PAGE_LIVESUCHE,
        \PAGE_HERSTELLER,
        \PAGE_SITEMAP,
        \PAGE_GRATISGESCHENK,
        \PAGE_WRB,
        \PAGE_PLUGIN,
        \PAGE_NEWSLETTERARCHIV,
        \PAGE_EIGENE,
        \PAGE_AUSWAHLASSISTENT,
        \PAGE_BESTELLABSCHLUSS,
        \PAGE_404,
        \PAGE_BESTELLSTATUS,
        \PAGE_NEWSMONAT,
        \PAGE_NEWSDETAIL,
        \PAGE_NEWSKATEGORIE
    ];

    /**
     * @var int
     */
    protected int $itemCount = 0;

    /**
     * @var bool|null
     */
    protected ?bool $show = null;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var string
     */
    protected string $url = '';

    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var string
     */
    protected string $templateFile = '';

    /**
     * @var PluginInterface|null
     */
    protected ?PluginInterface $plugin = null;

    /**
     * @var PluginInterface|null
     */
    protected ?PluginInterface $extension = null;

    /**
     * @var int
     */
    protected int $containerID = 0;

    /**
     * @var string|null
     */
    protected ?string $position = null;

    /**
     * @var string|string[]|null
     */
    protected string|array|null $title = null;

    /**
     * @var string|string[]|null
     */
    protected string|array|null $content = null;

    /**
     * @var int
     */
    protected int $id = 0;

    /**
     * @var int
     */
    protected int $baseType = 0;

    /**
     * @var int
     */
    protected int $customID = 0;

    /**
     * @var int
     */
    protected int $sort = 0;

    /**
     * @var bool
     */
    protected bool $isActive = true;

    /**
     * @var ArtikelListe|Artikel[]|null
     */
    protected $products;

    /**
     * @var array|mixed
     */
    protected $items = [];

    /**
     * @var string|null
     */
    protected ?string $json = null;

    /**
     * @var BoxInterface[]
     */
    protected array $children = [];

    /**
     * @var string
     */
    protected string $html = '';

    /**
     * @var string
     */
    protected string $renderedContent = '';

    /**
     * @var bool
     */
    protected bool $supportsRevisions = false;

    /**
     * @var array<int, bool|int[]>|null
     */
    protected ?array $filter = null;

    /**
     * @var array<int, int>
     */
    protected array $sortByPageID = [];

    /**
     * @var int
     */
    protected int $availableForPage = 0;

    /**
     * @inheritdoc
     */
    public function __construct(protected array $config)
    {
    }

    /**
     * @inheritdoc
     */
    public function getRenderer(): string
    {
        return DefaultRenderer::class;
    }

    /**
     * @param string $attrbute
     * @param string $method
     */
    public function addMapping(string $attrbute, string $method): void
    {
        self::$mapping[$attrbute] = $method;
    }

    /**
     * @inheritdoc
     */
    public function map(array $boxData): void
    {
        /** @var \stdClass $data */
        $data = first($boxData);
        if ($data->eTyp === null) {
            // containers do not have a lot of data..
            $data->eTyp      = Type::CONTAINER;
            $data->cTitel    = '';
            $data->cTemplate = 'box_container.tpl';
            $data->cName     = '';
        }
        $this->setID((int)$data->kBox);
        $this->setBaseType((int)$data->kBoxvorlage);
        $this->setCustomID((int)$data->kCustomID);
        $this->setContainerID((int)$data->kContainer);
        $this->setSort((int)$data->nSort);
        $this->setIsActive(true);
        $this->setAvailableForPage((int)($data->cVerfuegbar ?? 0));
        if ($this->products === null) {
            $this->products = new ArtikelListe();
        }
        if (!empty($data->kSprache)) {
            $this->setTitle([]);
            $this->setContent([]);
        } else {
            $this->setTitle(!empty($data->cTitel) ? $data->cTitel : $data->cName);
            $this->setContent('');
        }
        $this->setPosition($data->ePosition);
        $this->setType($data->eTyp);

        if ($this->getType() !== Type::PLUGIN && !\str_starts_with($data->cTemplate, 'boxes/')) {
            $data->cTemplate = 'boxes/' . $data->cTemplate;
        }
        $this->setTemplateFile($data->cTemplate);
        $this->setName($data->cName);

        foreach (self::$validPageTypes as $pageType) {
            $this->filter[$pageType] = false;
        }
        /** @var \stdClass $box */
        foreach ($boxData as $box) {
            $pageIDs            = \array_map('\intval', \explode(',', $box->pageIDs ?? ''));
            $sort               = \array_map('\intval', \explode(',', $box->sortBypageIDs ?? ''));
            $this->sortByPageID = \array_combine($pageIDs, $sort);
            if (!empty($box->cFilter)) {
                $this->filter[(int)$box->kSeite] = \array_map('\intval', \explode(',', $box->cFilter));
            } else {
                $pageVisibilities = \array_map('\intval', \explode(',', $box->pageVisibilities ?? ''));
                $filter           = \array_combine($pageIDs, $pageVisibilities);
                foreach ($filter as $pageID => $visibility) {
                    $this->filter[$pageID] = (bool)$visibility;
                }
            }
            if (!empty($box->kSprache)) {
                if (!\is_array($this->content)) {
                    $this->content = [];
                }
                if (!\is_array($this->title)) {
                    $this->title = [];
                }
                $this->content[(int)$box->kSprache] = $box->cInhalt;
                $this->title[(int)$box->kSprache]   = $box->cTitel;
            }
        }
        \ksort($this->filter);

        if (false($this->filter)) {
            $this->setIsActive(false);
        }
        if (!\is_bool($this->show)) {
            // may be overridden in concrete classes' __construct
            $this->setShow($this->isActive());
        }
        $this->init();
    }

    /**
     * @param int $pageID
     * @return bool|int[]
     */
    public function isVisibleOnPage(int $pageID)
    {
        return $this->filter[$pageID] ?? false;
    }

    /**
     * @inheritdoc
     */
    public function isBoxVisible(int $pageType = \PAGE_UNBEKANNT, int $pageID = 0): bool
    {
        if ($this->show === false) {
            return false;
        }
        $vis = empty($this->filter) || (isset($this->filter[$pageType]) && $this->filter[$pageType] === true);

        if ($vis === false && $pageID > 0 && GeneralObject::isCountable($pageType, $this->filter)) {
            $vis = \in_array($pageID, $this->filter[$pageType], true);
        }

        return $vis;
    }

    /**
     * @return bool
     */
    public function show(): bool
    {
        return $this->show ?? false;
    }

    /**
     * @return bool
     */
    public function getShow(): bool
    {
        return $this->show ?? false;
    }

    /**
     * @param bool $show
     */
    public function setShow(bool $show): void
    {
        $this->show = $show;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getURL(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setURL(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTemplateFile(): string
    {
        return $this->templateFile;
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile(string $templateFile): void
    {
        $this->templateFile = $templateFile;
    }

    /**
     * @return null|PluginInterface
     */
    public function getPlugin(): ?PluginInterface
    {
        return $this->plugin;
    }

    /**
     * @param null|PluginInterface $plugin
     */
    public function setPlugin(?PluginInterface $plugin): void
    {
        $this->plugin = $plugin;
    }

    /**
     * @return null|PluginInterface
     */
    public function getExtension(): ?PluginInterface
    {
        return $this->extension;
    }

    /**
     * @param null|PluginInterface $extension
     */
    public function setExtension(?PluginInterface $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * @return int
     */
    public function getContainerID(): int
    {
        return $this->containerID;
    }

    /**
     * @param int $containerID
     */
    public function setContainerID(int $containerID): void
    {
        $this->containerID = $containerID;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    /**
     * @inheritdoc
     */
    public function getTitle($idx = null): string
    {
        if (\is_string($this->title)) {
            return $this->title;
        }
        $idx = $idx ?? Shop::getLanguageID();

        return $this->title[$idx] ?? '';
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @inheritdoc
     */
    public function getContent($idx = null): string
    {
        if (\is_string($this->content)) {
            return $this->content;
        }
        $idx = $idx ?? Shop::getLanguageID();

        return $this->content[$idx] ?? '';
    }

    /**
     * @param string|array $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setID(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getBaseType(): int
    {
        return $this->baseType;
    }

    /**
     * @param int $type
     */
    public function setBaseType(int $type): void
    {
        $this->baseType = $type;
    }

    /**
     * @inheritdoc
     */
    public function getCustomID(): int
    {
        return $this->customID;
    }

    /**
     * @inheritdoc
     */
    public function setCustomID(int $id): void
    {
        $this->customID = $id;
    }

    /**
     * @inheritdoc
     */
    public function getSort(?int $pageID = null): int
    {
        return $pageID === null ? $this->sort : $this->sortByPageID[$pageID] ?? 0;
    }

    /**
     * @inheritdoc
     */
    public function setSort(int $sort, ?int $pageID = null): void
    {
        if ($pageID !== null) {
            $this->sortByPageID[$pageID] = $sort;
        } else {
            $this->sort = $sort;
        }
    }

    /**
     * @inheritdoc
     */
    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    /**
     * @inheritdoc
     */
    public function setItemCount(int $count): void
    {
        $this->itemCount = $count;
    }

    /**
     * @inheritdoc
     */
    public function supportsRevisions(): bool
    {
        return $this->supportsRevisions;
    }

    /**
     * @inheritdoc
     */
    public function setSupportsRevisions(bool $supportsRevisions): void
    {
        $this->supportsRevisions = $supportsRevisions;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @inheritdoc
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @inheritdoc
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getShowCompat(): string
    {
        return $this->show === true ? 'Y' : 'N';
    }

    /**
     * @param string $show
     */
    public function setShowCompat(string $show): void
    {
        $this->show = $show === 'Y';
    }

    /**
     * @inheritdoc
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @inheritdoc
     */
    public function setProducts($products): void
    {
        $this->products = $products;
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function setItems($items): void
    {
        $this->items = $items;
    }

    /**
     * @inheritdoc
     */
    public function getFilter(int $idx = null)
    {
        return $idx === null ? $this->filter : $this->filter[$idx] ?? true;
    }

    /**
     * @inheritdoc
     */
    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @inheritdoc
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getJSON(): string
    {
        return $this->json ?? '';
    }

    /**
     * @inheritdoc
     */
    public function setJSON(string $json): void
    {
        $this->json = $json;
    }

    /**
     * @inheritdoc
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function setChildren(array $chilren): void
    {
        $this->children = $chilren[$this->getID()] ?? [];
    }

    /**
     * @return string
     */
    public function getHTML(): string
    {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHTML(string $html): void
    {
        $this->html = $html;
    }

    /**
     * @inheritdoc
     */
    public function getRenderedContent(): string
    {
        return $this->renderedContent;
    }

    /**
     * @inheritdoc
     */
    public function setRenderedContent(string $renderedContent): void
    {
        $this->renderedContent = $renderedContent;
    }

    /**
     * special json string for sidebar clouds
     *
     * @param \stdClass[] $cloud
     * @param string      $speed
     * @param string      $opacity
     * @param bool        $color
     * @param bool        $colorHover
     * @return string
     */
    public static function getJSONString(
        array $cloud,
        string $speed = '1',
        string $opacity = '0.2',
        bool $color = false,
        bool $colorHover = false
    ): string {
        if (\count($cloud) === 0) {
            return '';
        }
        $iCur                       = 0;
        $iMax                       = 15;
        $tags                       = [];
        $tags['options']['speed']   = $speed;
        $tags['options']['opacity'] = $opacity;
        $getTagColor                = static function (): string {
            $color = '';
            $codes = ['00', '33', '66', '99', 'CC', 'FF'];
            for ($i = 0; $i < 3; $i++) {
                $color .= $codes[\random_int(0, \count($codes) - 1)];
            }

            return '0x' . $color;
        };

        foreach ($cloud as $item) {
            if ($iCur++ >= $iMax) {
                break;
            }
            $name           = $item->cName ?? $item->cSuche;
            $randomColor    = (!$color || !$colorHover) ? $getTagColor() : '';
            $name           = \urlencode($name);
            $name           = \str_replace('+', ' ', $name); /* fix :) */
            $tags['tags'][] = [
                'name'  => $name,
                'url'   => $item->cURL,
                'size'  => (\count($cloud) <= 5) ? '100' : (string)($item->Klasse * 10), /* 10 bis 100 */
                'color' => $color ?: $randomColor,
                'hover' => $colorHover ?: $randomColor
            ];
        }

        return \urlencode(\json_encode($tags, \JSON_THROW_ON_ERROR));
    }

    /**
     * @return int
     */
    public function getContainerCheckCompat(): int
    {
        return $this->getBaseType() === \BOX_CONTAINER ? 1 : 0;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        $res           = \get_object_vars($this);
        $res['config'] = '*truncated*';

        return $res;
    }

    /**
     * @inheritdoc
     */
    public function init(): void
    {
    }

    /**
     * @return int
     */
    public function getAvailableForPage(): int
    {
        return $this->availableForPage;
    }

    /**
     * @param int $availableForPage
     */
    public function setAvailableForPage(int $availableForPage): void
    {
        $this->availableForPage = $availableForPage;
    }
}
