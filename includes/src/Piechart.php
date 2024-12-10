<?php

declare(strict_types=1);

namespace JTL;

use stdClass;

/**
 * Class Piechart
 * @package JTL
 */
class Piechart extends Chartdata
{
    /**
     * @param string $name
     * @param array  $data
     * @return $this
     */
    public function addSerie($name, array $data): self
    {
        if ($this->series === null) {
            $this->series = [];
        }
        $serie          = new stdClass();
        $serie->type    = 'pie';
        $serie->name    = $name;
        $serie->data    = $data;
        $this->series[] = $serie;

        return $this;
    }
}
