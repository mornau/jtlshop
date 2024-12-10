<?php

declare(strict_types=1);

namespace JTL\Plugin\Data;

use Illuminate\Support\Collection;
use stdClass;

use function Functional\reindex;

/**
 * Class Widget
 * @package JTL\Plugin\Data
 */
class Widget
{
    /**
     * @var Collection<stdClass>
     */
    private Collection $widgets;

    /**
     * Widget constructor.
     */
    public function __construct()
    {
        $this->widgets = new Collection();
    }

    /**
     * @param stdClass[] $data
     * @param string     $adminPath
     * @return $this
     */
    public function load(array $data, string $adminPath): self
    {
        foreach ($data as $widget) {
            $widget->nPos        = (int)$widget->nPos;
            $widget->bExpanded   = (int)$widget->bExpanded;
            $widget->bActive     = (int)$widget->bActive;
            $widget->kWidget     = (int)$widget->kWidget;
            $widget->id          = $widget->kWidget;
            $widget->kPlugin     = (int)$widget->kPlugin;
            $widget->isExtension = true;
            $widget->classFile   = $adminPath . \PFAD_PLUGIN_WIDGET . $widget->cClass . '.php';
            $widget->className   = '\Plugin' . $widget->namespace . $widget->cClass;
            $this->widgets->push($widget);
        }

        return $this;
    }

    /**
     * @param int $id
     * @return stdClass|null
     */
    public function getWidgetByID(int $id): ?stdClass
    {
        return $this->widgets->firstWhere('id', $id);
    }

    /**
     * @return Collection<stdClass>
     */
    public function getWidgets(): Collection
    {
        return $this->widgets;
    }

    /**
     * @return array<int, stdClass>
     */
    public function getWidgetsAssoc(): array
    {
        return reindex($this->widgets, static function (stdClass $e): int {
            return $e->kWidget;
        });
    }

    /**
     * @param Collection<stdClass> $widgets
     */
    public function setWidgets(Collection $widgets): void
    {
        $this->widgets = $widgets;
    }

    /**
     * @param stdClass $widget
     * @return Collection<stdClass>
     */
    public function addWidget(stdClass $widget): Collection
    {
        $this->widgets->push($widget);

        return $this->widgets;
    }
}
