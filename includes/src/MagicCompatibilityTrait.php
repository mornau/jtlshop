<?php

declare(strict_types=1);

namespace JTL;

/**
 * Trait MagicCompatibilityTrait
 *
 * allows a backwards compatable access to class properties
 * that are now hidden behind getters and setters via a simple list of mappings
 *
 */
trait MagicCompatibilityTrait
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * @param string $value
     * @return string|array|null
     */
    private static function getMapping($value)
    {
        return self::$mapping[$value] ?? null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        \trigger_error(__CLASS__ . ': getter should be used to get ' . $name, \E_USER_DEPRECATED);
        if (\COMPATIBILITY_TRACE_DEPTH > 0 && \error_reporting() >= \E_USER_DEPRECATED) {
            Shop::dbg($name, false, 'Backtrace for', \COMPATIBILITY_TRACE_DEPTH + 1);
        }
        /** @var array|string|null $mapped */
        $mapped = self::getMapping($name);
        if ($mapped !== null) {
            if (\is_array($mapped) && \count($mapped) === 2) {
                $method1 = $mapped[0];
                $method2 = 'get' . $mapped[1];

                return \call_user_func([$this->$method1(), $method2]);
            }
            $method = 'get' . $mapped;

            return $this->$method();
        }
        if (\property_exists($this, $name)) {
            return $this->$name;
        }

        return $this->data[$name] ?? null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function __set(string $name, mixed $value)
    {
        \trigger_error(__CLASS__ . ': setter should be used to set ' . $name, \E_USER_DEPRECATED);
        if (\COMPATIBILITY_TRACE_DEPTH > 0 && \error_reporting() >= \E_USER_DEPRECATED) {
            Shop::dbg($name, false, 'Backtrace for', \COMPATIBILITY_TRACE_DEPTH + 1);
        }
        if (($mapped = self::getMapping($name)) !== null) {
            if (\is_array($mapped) && \count($mapped) === 2) {
                $method1 = $mapped[0];
                $method2 = 'set' . $mapped[1];

                return \call_user_func([$this->$method1(), $method2], $value);
            }
            $method = 'set' . $mapped;

            return $this->$method($value);
        }
        if (\property_exists($this, $name)) {
            $this->$name = $value;

            return $this;
        }
        \trigger_error(__CLASS__ . ': setter could not find property ' . $name, \E_USER_DEPRECATED);
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return \property_exists($this, $name) || self::getMapping($name) !== null;
    }
}
