<?php

declare(strict_types=1);

namespace JTL;

use JTL\DB\DbInterface;
use stdClass;

use function Functional\first;

/**
 * Class Slider
 * @package JTL
 */
class Slider implements IExtensionPoint
{
    use MagicCompatibilityTrait;

    /**
     * @var int
     */
    private int $id = 0;

    /**
     * @var string
     */
    private string $name = '';

    /**
     * @var int
     */
    private int $languageID = 0;

    /**
     * @var int
     */
    private int $customerGroupID = 0;

    /**
     * @var int
     */
    private int $pageType = 0;

    /**
     * @var string
     */
    private string $theme = '';

    /**
     * @var bool
     */
    private bool $isActive = false;

    /**
     * @var string
     */
    private string $effects = 'random';

    /**
     * @var int
     */
    private int $pauseTime = 3000;

    /**
     * @var bool
     */
    private bool $thumbnail = false;

    /**
     * @var int
     */
    private int $animationSpeed = 500;

    /**
     * @var bool
     */
    private bool $pauseOnHover = false;

    /**
     * @var Slide[]
     */
    private array $slides = [];

    /**
     * @var bool
     */
    private bool $controlNav = true;

    /**
     * @var bool
     */
    private bool $randomStart = false;

    /**
     * @var bool
     */
    private bool $directionNav = true;

    /**
     * @var bool
     */
    private bool $useKB = true;

    /**
     * @var array<string, string>
     */
    private static array $mapping = [
        'bAktiv'          => 'IsActive',
        'kSlider'         => 'ID',
        'cName'           => 'Name',
        'kSprache'        => 'LanguageID',
        'nSeitentyp'      => 'PageType',
        'cTheme'          => 'Theme',
        'cEffects'        => 'Effects',
        'nPauseTime'      => 'PauseTime',
        'bThumbnail'      => 'Thumbnail',
        'nAnimationSpeed' => 'AnimationSpeed',
        'bPauseOnHover'   => 'PauseOnHover',
        'oSlide_arr'      => 'Slides',
        'bControlNav'     => 'ControlNav',
        'bRandomStart'    => 'RandomStart',
        'bDirectionNav'   => 'DirectionNav',
        'bUseKB'          => 'UseKB',
        'kKundengruppe'   => 'CustomerGroupID'
    ];

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     * Slider constructor.
     * @param DbInterface $db
     */
    public function __construct(private readonly DbInterface $db)
    {
    }

    /**
     * @param string $value
     * @return string|null
     */
    private function getMapping(string $value): ?string
    {
        return self::$mapping[$value] ?? null;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function init($id)
    {
        $loaded = $this->load($id);
        if ($id > 0 && $loaded === true) {
            Shop::Smarty()->assign('oSlider', $this);
        }

        return $this;
    }

    /**
     * @param stdClass $data
     * @return $this
     */
    public function set(stdClass $data): self
    {
        foreach (\get_object_vars($data) as $field => $value) {
            if (($mapping = $this->getMapping($field)) !== null) {
                $method = 'set' . $mapping;
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @param int  $int
     * @param bool $active
     * @return bool
     */
    public function load(int $int = 0, bool $active = true): bool
    {
        if ($int <= 0 && $this->id <= 0) {
            return false;
        }
        $activeSQL = $active ? ' AND bAktiv = 1 ' : '';
        if ($int === 0) {
            $int = $this->id;
        }
        $data = $this->db->getObjects(
            'SELECT *, tslider.kSlider AS id 
                FROM tslider
                LEFT JOIN tslide
                    ON tslider.kSlider = tslide.kSlider
                WHERE tslider.kSlider = :sliderID' . $activeSQL .
            ' ORDER BY tslide.nSort',
            ['sliderID' => $int]
        );
        /** @var stdClass|null $first */
        $first = first($data);
        if ($first === null) {
            return false;
        }
        $this->setID($first->id);
        foreach ($data as $slideData) {
            $slideData->kSlider = $this->getID();
            if ($slideData->kSlide !== null) {
                $slide = new Slide(0, $this->db);
                $slide->map($slideData);
                $this->slides[] = $slide;
            }
        }
        $this->set($first);

        return $this->getID() > 0 && \count($this->slides) > 0;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        return $this->id > 0
            ? $this->update()
            : $this->append();
    }

    /**
     * @return bool
     */
    private function append(): bool
    {
        $slider = new stdClass();
        foreach (self::$mapping as $type => $methodName) {
            $method        = 'get' . $methodName;
            $slider->$type = $this->$method();
            if (\is_bool($slider->$type)) {
                $slider->$type = (int)$slider->$type;
            }
        }
        unset($slider->oSlide_arr, $slider->slides, $slider->kSlider);

        $kSlider = $this->db->insert('tslider', $slider);

        if ($kSlider > 0) {
            $this->id = $kSlider;

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function update(): bool
    {
        $slider = new stdClass();
        foreach (self::$mapping as $type => $methodName) {
            $method        = 'get' . $methodName;
            $slider->$type = $this->$method();
            if (\is_bool($slider->$type)) {
                $slider->$type = (int)$slider->$type;
            }
        }
        unset($slider->oSlide_arr, $slider->slides, $slider->kSlider);

        return $this->db->update('tslider', 'kSlider', $this->getID(), $slider) >= 0;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $id = $this->getID();
        if ($id !== 0) {
            $affected = $this->db->delete('tslider', 'kSlider', $id);
            $this->db->delete('textensionpoint', ['cClass', 'kInitial'], ['Slider', $id]);
            if ($affected > 0) {
                foreach ($this->slides as $slide) {
                    $slide->delete();
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @param int|string $kSlider
     */
    public function setID(int|string $kSlider): void
    {
        $this->id = (int)$kSlider;
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
     * @return int
     */
    public function getLanguageID(): int
    {
        return $this->languageID;
    }

    /**
     * @param int|string $languageID
     */
    public function setLanguageID(int|string $languageID): void
    {
        $this->languageID = (int)$languageID;
    }

    /**
     * @return int
     */
    public function getCustomerGroupID(): int
    {
        return $this->customerGroupID;
    }

    /**
     * @param int|string $customerGroupID
     */
    public function setCustomerGroupID(int|string $customerGroupID): void
    {
        $this->customerGroupID = (int)$customerGroupID;
    }

    /**
     * @return int
     */
    public function getPageType(): int
    {
        return $this->pageType;
    }

    /**
     * @param int|string $pageType
     */
    public function setPageType(int|string $pageType): void
    {
        $this->pageType = (int)$pageType;
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool|int|string $isActive
     */
    public function setIsActive(bool|int|string $isActive): void
    {
        $this->isActive = (bool)$isActive;
    }

    /**
     * @return string
     */
    public function getEffects(): string
    {
        return $this->effects;
    }

    /**
     * @param string $effects
     */
    public function setEffects(string $effects): void
    {
        $this->effects = $effects;
    }

    /**
     * @return int
     */
    public function getPauseTime(): int
    {
        return $this->pauseTime;
    }

    /**
     * @param int|string $pauseTime
     */
    public function setPauseTime(int|string $pauseTime): void
    {
        $this->pauseTime = (int)$pauseTime;
    }

    /**
     * @return bool
     */
    public function getThumbnail(): bool
    {
        return $this->thumbnail;
    }

    /**
     * @param bool|int|string $thumbnail
     */
    public function setThumbnail(bool|int|string $thumbnail): void
    {
        $this->thumbnail = (bool)$thumbnail;
    }

    /**
     * @return int
     */
    public function getAnimationSpeed(): int
    {
        return $this->animationSpeed;
    }

    /**
     * @param int|string $animationSpeed
     */
    public function setAnimationSpeed(int|string $animationSpeed): void
    {
        $this->animationSpeed = (int)$animationSpeed;
    }

    /**
     * @return bool
     */
    public function getPauseOnHover(): bool
    {
        return $this->pauseOnHover;
    }

    /**
     * @param bool|int|string $pauseOnHover
     */
    public function setPauseOnHover(bool|int|string $pauseOnHover): void
    {
        $this->pauseOnHover = (bool)$pauseOnHover;
    }

    /**
     * @return Slide[]
     */
    public function getSlides(): array
    {
        return $this->slides;
    }

    /**
     * @param Slide[] $slides
     */
    public function setSlides(array $slides): void
    {
        $this->slides = $slides;
    }

    /**
     * @return bool
     */
    public function getControlNav(): bool
    {
        return $this->controlNav;
    }

    /**
     * @param bool|int|string $controlNav
     */
    public function setControlNav(bool|int|string $controlNav): void
    {
        $this->controlNav = (bool)$controlNav;
    }

    /**
     * @return bool
     */
    public function getRandomStart(): bool
    {
        return $this->randomStart;
    }

    /**
     * @param bool|int|string $randomStart
     */
    public function setRandomStart(bool|int|string $randomStart): void
    {
        $this->randomStart = (bool)$randomStart;
    }

    /**
     * @return bool
     */
    public function getDirectionNav(): bool
    {
        return $this->directionNav;
    }

    /**
     * @param bool|int|string $directionNav
     */
    public function setDirectionNav(bool|int|string $directionNav): void
    {
        $this->directionNav = (bool)$directionNav;
    }

    /**
     * @return bool
     */
    public function getUseKB(): bool
    {
        return $this->useKB;
    }

    /**
     * @param bool|int|string $useKB
     */
    public function setUseKB(bool|int|string $useKB): void
    {
        $this->useKB = (bool)$useKB;
    }
}
