<?php

declare(strict_types=1);

namespace JTL\ServiceReport\Report;

use JTL\Backend\Status;
use JTL\Shop;

use function Functional\map;
use function Functional\reindex;

class DatabaseStatistics implements ReportInterface
{
    /**
     * @return array<string, string>
     */
    public function getData(): array
    {
        return map(
            reindex(
                Status::getInstance(Shop::Container()->getDB(), Shop::Container()->getCache())->getMySQLStats(),
                fn($data) => $data['key']
            ),
            fn($item) => $item['value']
        );
    }
}
