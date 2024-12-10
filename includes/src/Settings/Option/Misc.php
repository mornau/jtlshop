<?php

declare(strict_types=1);

namespace JTL\Settings\Option;

use JTL\Settings\Section;

enum Misc: string implements OptionInterface
{
    case LIVESEARCH_TOP_COUNT      = 'sonstiges_livesuche_all_top_count';
    case LIVESEARCH_RECENT_QUERIES = 'sonstiges_livesuche_all_last_count';
    case FREE_GIFTS_USE            = 'sonstiges_gratisgeschenk_nutzen';
    case FREE_GIFTS_QTY            = 'sonstiges_gratisgeschenk_anzahl';
    case FREE_GIFTS_SORT           = 'sonstiges_gratisgeschenk_sortierung';

    public function getValue(): string
    {
        return $this->value;
    }

    public function getSection(): Section
    {
        return Section::MISC;
    }
}
