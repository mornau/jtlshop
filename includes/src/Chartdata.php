<?php

declare(strict_types=1);

namespace JTL;

use Exception;
use stdClass;

/**
 * Class Chartdata
 * @package JTL
 */
class Chartdata
{
    /**
     * @var bool
     */
    protected $bActive;

    /**
     * @var object
     */
    protected $xAxis;

    /**
     * @var stdClass[]|null
     */
    protected $series;

    /**
     * @var string
     */
    protected $xAxisJSON;

    /**
     * @var string
     */
    protected $seriesJSON;

    /**
     * @var string
     */
    protected $url;

    /**
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if (\is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return $this
     * @throws Exception
     */
    public function __set(string $name, mixed $value)
    {
        $method = 'set' . $name;
        if ($name === 'mapper' || !\method_exists($this, $method)) {
            throw new Exception('Invalid Query property');
        }
        $this->$method($value);

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get(string $name)
    {
        $method = 'get' . $name;
        if ($name === 'mapper' || !\method_exists($this, $method)) {
            throw new Exception('Invalid Query property');
        }

        return $this->$method();
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $methods = \get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . \ucfirst($key);
            if (\in_array($method, $methods, true)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $array   = [];
        $members = \array_keys(\get_object_vars($this));
        foreach ($members as $member) {
            $array[\mb_substr($member, 1)] = $this->$member;
        }

        return $array;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function setActive($active): self
    {
        $this->bActive = (bool)$active;

        return $this;
    }

    /**
     * @param object $axis
     * @return $this
     */
    public function setAxis($axis): self
    {
        $this->xAxis = $axis;

        return $this;
    }

    /**
     * @param array $series
     * @return $this
     */
    public function setSeries($series): self
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->bActive;
    }

    /**
     * @return object|null
     */
    public function getAxis()
    {
        return $this->xAxis;
    }

    /**
     * @return array|null
     */
    public function getSeries(): ?array
    {
        return $this->series;
    }

    /**
     * @return string|null
     */
    public function getAxisJSON(): ?string
    {
        return $this->xAxisJSON;
    }

    /**
     * @return string|null
     */
    public function getSeriesJSON(): ?string
    {
        return $this->seriesJSON;
    }

    /**
     * @return $this
     */
    public function memberToJSON(): self
    {
        $this->seriesJSON = \json_encode($this->series);
        $this->xAxisJSON  = \json_encode($this->xAxis);

        return $this;
    }
}
