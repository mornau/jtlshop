<?php

declare(strict_types=1);

namespace JTL\Sitemap\SchemaRenderers;

/**
 * Class AbstractSchemaRenderer
 * @package JTL\Sitemap\SchemaRenderers
 */
abstract class AbstractSchemaRenderer implements SchemaRendererInterface
{
    /**
     * @var array<string, string[]>
     */
    protected array $config;

    /**
     * @var string
     */
    protected string $xmlHeader = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

    /**
     * @return array<string, string[]>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array<string, string[]> $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getXmlHeader(): string
    {
        return $this->xmlHeader;
    }

    /**
     * @param string $xmlHeader
     */
    public function setXmlHeader(string $xmlHeader): void
    {
        $this->xmlHeader = $xmlHeader;
    }
}
