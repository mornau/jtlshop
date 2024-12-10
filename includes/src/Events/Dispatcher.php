<?php

declare(strict_types=1);

namespace JTL\Events;

use JTL\SingletonTrait;
use stdClass;

use function Functional\pluck;

/**
 * Class Dispatcher
 * @package JTL\Events
 */
final class Dispatcher
{
    use SingletonTrait;

    /**
     * The registered event listeners.
     *
     * @var array<string, array<object{listener: callable, priority: int}&stdClass>>
     */
    private array $listeners = [];

    /**
     * The wildcard listeners.
     *
     * @var array<string, array<object{listener: callable, priority: int}&stdClass>>
     */
    private array $wildcards = [];

    /**
     * Determine if a given event has listeners.
     *
     * @param string $eventName
     * @return bool
     */
    public function hasListeners(string $eventName): bool
    {
        return isset($this->listeners[$eventName]) || isset($this->wildcards[$eventName]);
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param string[]|string $eventNames
     * @param callable        $listener
     * @param int             $priority
     */
    public function listen(array|string $eventNames, callable $listener, int $priority = 5): void
    {
        foreach ((array)$eventNames as $event) {
            $item = (object)['listener' => $listener, 'priority' => $priority];
            if (\str_contains($event, '*')) {
                $this->wildcards[$event][] = $item;
            } else {
                $this->listeners[$event][] = $item;
            }
        }
    }

    /**
     * @param int      $hookID
     * @param callable $listener
     * @param int      $priority
     * @return void
     * @since 5.2.0
     */
    public function hookInto(int $hookID, callable $listener, int $priority = 5): void
    {
        $this->listeners['shop.hook.' . $hookID][] = (object)['listener' => $listener, 'priority' => $priority];
    }

    /**
     * Fire an event and call the listeners.
     *
     * @param string       $eventName
     * @param array|object $arguments
     */
    public function fire(string $eventName, $arguments = []): void
    {
        foreach ($this->getListeners($eventName) as $listener) {
            $listener($arguments);
        }
    }

    public function getData(int $hookID, $result, ...$arguments)
    {
        foreach ($this->getListeners('shop.hook.' . $hookID) as $listener) {
            $result = $listener($result, ...$arguments);
        }

        return $result;
    }

    /**
     * Remove a set of listeners from the dispatcher.
     *
     * @param string $eventName
     */
    public function forget(string $eventName): void
    {
        if (\str_contains($eventName, '*')) {
            if (isset($this->wildcards[$eventName])) {
                unset($this->wildcards[$eventName]);
            }
        } elseif (isset($this->listeners[$eventName])) {
            unset($this->listeners[$eventName]);
        }
    }

    /**
     * Get all of the listeners for a given event name.
     *
     * @param string $eventName
     * @return \Closure[]
     */
    public function getListeners(string $eventName): array
    {
        $listeners = $this->getWildcardListeners($eventName);
        if (isset($this->listeners[$eventName])) {
            $listeners = \array_merge($listeners, $this->listeners[$eventName]);
        }
        \usort($listeners, $this->sortByPriority(...));

        return pluck($listeners, 'listener');
    }

    /**
     * @param stdClass $a
     * @param stdClass $b
     * @return int
     */
    private function sortByPriority(stdClass $a, stdClass $b): int
    {
        return $a->priority <=> $b->priority;
    }

    /**
     * Get the wildcard listeners for the event.
     *
     * @param string $eventName
     * @return array
     */
    private function getWildcardListeners(string $eventName): array
    {
        $wildcards = [];
        foreach ($this->wildcards as $key => $listeners) {
            if (\fnmatch($key, $eventName)) {
                $wildcards[] = $listeners;
            }
        }

        return \array_merge(...$wildcards);
    }
}
