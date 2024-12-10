<?php

declare(strict_types=1);

namespace JTL\ServiceReport\Report;

interface ReportInterface
{
    public function getData(): array;
}
