<?php

declare(strict_types=1);

namespace JTL;

use JTL\DB\DbInterface;
use stdClass;

/**
 * Class Slide
 * @package JTL
 */
class Slide
{
    use MagicCompatibilityTrait;

    /**
     * @var int
     */
    private int $id = 0;

    /**
     * @var int
     */
    private int $sliderID = 0;

    /**
     * @var string
     */
    private string $title = '';

    /**
     * @var string
     */
    private string $image = '';

    /**
     * @var string
     */
    private string $text = '';

    /**
     * @var string
     */
    private string $thumbnail = '';

    /**
     * @var string
     */
    private string $link = '';

    /**
     * @var int
     */
    private int $sort = 0;

    /**
     * @var string
     */
    private string $absoluteImage = '';

    /**
     * @var string
     */
    private string $absoluteThumbnail = '';

    /**
     * @var DbInterface
     */
    private DbInterface $db;

    /**
     * @var array<string, string>
     */
    private static array $mapping = [
        'kSlide'            => 'ID',
        'kSlider'           => 'SliderID',
        'cTitel'            => 'Title',
        'cBild'             => 'Image',
        'cText'             => 'Text',
        'cThumbnail'        => 'Thumbnail',
        'cLink'             => 'Link',
        'nSort'             => 'Sort',
        'cBildAbsolut'      => 'AbsoluteImage',
        'cThumbnailAbsolut' => 'AbsoluteThumbnail'
    ];

    /**
     *
     */
    private function __clone()
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
     * @param int              $id
     * @param DbInterface|null $db
     */
    public function __construct(int $id = 0, ?DbInterface $db = null)
    {
        $this->db = $db ?? Shop::Container()->getDB();
        if ($id > 0) {
            $this->load($id);
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function load(int $id = 0): bool
    {
        if ($id > 0 || $this->id > 0) {
            if ($id === 0) {
                $id = $this->id;
            }

            $slide = $this->db->select('tslide', 'kSlide', $id);
            if ($slide !== null) {
                $this->set($slide);

                return true;
            }
        }

        return false;
    }

    /**
     * @param stdClass $data
     * @return $this
     */
    public function map(stdClass $data): self
    {
        foreach (\get_object_vars($data) as $field => $value) {
            if (($mapping = $this->getMapping($field)) !== null) {
                $method = 'set' . $mapping;
                $this->$method($value);
            }
        }
        $this->setAbsoluteImagePaths();

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
     * @return $this
     */
    private function setAbsoluteImagePaths(): self
    {
        $basePath                = Shop::getImageBaseURL();
        $this->absoluteImage     = \str_starts_with($this->image, 'http://')
        || \str_starts_with($this->image, 'https://')
            ? $this->image
            : $basePath . $this->image;
        $this->absoluteThumbnail = \str_starts_with($this->thumbnail, 'http:')
        || \str_starts_with($this->thumbnail, 'https:')
            ? $this->thumbnail
            : $basePath . $this->thumbnail;

        return $this;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!empty($this->image)) {
            if (\str_starts_with($this->image, 'Bilder/')) {
                $this->setThumbnail(\PFAD_MEDIAFILES . 'Bilder/.tmb/' . \basename($this->getThumbnail()));
            } else {
                $this->setThumbnail(\STORAGE_OPC . '.tmb/' . \basename($this->getThumbnail()));
            }
            $shopURL = Shop::getURL();
            $path    = \parse_url($shopURL . '/', \PHP_URL_PATH) ?: 'invalid';
            if (\str_starts_with($this->image, $shopURL)) {
                $this->image = \ltrim(\substr($this->image, \mb_strlen($shopURL)), '/');
            } elseif (\str_starts_with($this->image, $path)) {
                $this->image = \ltrim(\substr($this->image, \mb_strlen($path)), '/');
            }
        }

        return $this->id === 0
            ? $this->append()
            : $this->update() > 0;
    }

    /**
     * @return int
     */
    private function update(): int
    {
        $slide = new stdClass();
        if (!empty($this->getThumbnail())) {
            $slide->cThumbnail = $this->getThumbnail();
        }
        $slide->kSlider = $this->getSliderID();
        $slide->cTitel  = $this->getTitle();
        $slide->cBild   = $this->getImage();
        $slide->nSort   = $this->getSort();
        $slide->cLink   = $this->getLink();
        $slide->cText   = $this->getText();

        return $this->db->update('tslide', 'kSlide', $this->getID(), $slide);
    }

    /**
     * @return bool
     */
    private function append(): bool
    {
        if (empty($this->image)) {
            return false;
        }
        $slide = new stdClass();
        foreach (self::$mapping as $type => $methodName) {
            $method       = 'get' . $methodName;
            $slide->$type = $this->$method();
        }
        unset($slide->cBildAbsolut, $slide->cThumbnailAbsolut, $slide->kSlide);
        if ($this->sort === 0) {
            $sort         = $this->db->getSingleObject(
                'SELECT nSort
                    FROM tslide
                    WHERE kSlider = :sliderID
                    ORDER BY nSort DESC LIMIT 1',
                ['sliderID' => $this->sliderID]
            );
            $slide->nSort = ($sort === null || (int)$sort->nSort === 0) ? 1 : ($sort->nSort + 1);
        }
        $id = $this->db->insert('tslide', $slide);
        if ($id > 0) {
            $this->id = $id;

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->id > 0 && $this->db->delete('tslide', 'kSlide', $this->id) > 0;
    }

    /**
     * @return int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setID(int|string $id): void
    {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getSliderID(): int
    {
        return $this->sliderID;
    }

    /**
     * @param int|string $sliderID
     */
    public function setSliderID(int|string $sliderID): void
    {
        $this->sliderID = (int)$sliderID;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     */
    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int|string $sort
     */
    public function setSort(int|string $sort): void
    {
        $this->sort = (int)$sort;
    }

    /**
     * @return string
     */
    public function getAbsoluteImage(): string
    {
        return $this->absoluteImage;
    }

    /**
     * @param string $absoluteImage
     */
    public function setAbsoluteImage(string $absoluteImage): void
    {
        $this->absoluteImage = $absoluteImage;
    }

    /**
     * @return string
     */
    public function getAbsoluteThumbnail(): string
    {
        return $this->absoluteThumbnail;
    }

    /**
     * @param string $absoluteThumbnail
     */
    public function setAbsoluteThumbnail(string $absoluteThumbnail): void
    {
        $this->absoluteThumbnail = $absoluteThumbnail;
    }
}
