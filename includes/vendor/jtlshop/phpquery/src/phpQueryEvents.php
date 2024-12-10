<?php declare(strict_types=1);

namespace JTL\phpQuery;

use DOMElement;
use DOMNode;
use Exception;

/**
 * Event handling class.
 *
 * @author Tobiasz Cudnik
 * @package JTL\phpQuery
 */
abstract class phpQueryEvents
{
    /**
     * Trigger a type of event on every matched element.
     *
     * @param DOMNode|phpQueryObject|string $document
     * @param string                        $type
     * @param array|mixed                         $data
     * @param mixed                         $node
     * @throws Exception
     * @TODO exclusive events (with !)
     * @TODO global events (test)
     * @TODO support more than event in $type (space-separated)
     */
    public static function trigger($document, $type, $data = [], $node = null): void
    {
        // trigger: function(type, data, elem, donative, extra) {
        $documentID = phpQuery::getDocumentID($document);
        $namespace  = null;
        if (\strpos($type, '.') !== false) {
            [$name, $namespace] = \explode('.', $type);
        } else {
            $name = $type;
        }
        if (!$node) {
            if (self::issetGlobal($documentID, $type)) {
                $pq = phpQuery::getDocument($documentID);
                // TODO check add($pq->document)
                $pq->find('*')->add($pq->document)
                   ->trigger($type, $data);
            }
        } else {
            if (isset($data[0]) && $data[0] instanceof DOMEvent) {
                $event                = $data[0];
                $event->relatedTarget = $event->target;
                $event->target        = $node;
                $data                 = \array_slice($data, 1);
            } else {
                $event = new DOMEvent([
                    'type'      => $type,
                    'target'    => $node,
                    'timeStamp' => \time(),
                ]);
            }
            while ($node) {
                $event->currentTarget = $node;
                $eventNode            = self::getNode($documentID, $node);
                if (isset($eventNode->eventHandlers)) {
                    foreach ($eventNode->eventHandlers as $eventType => $handlers) {
                        $eventNamespace = null;
                        if (\strpos($type, '.') !== false) {
                            [$eventName, $eventNamespace] = \explode('.', $eventType);
                        } else {
                            $eventName = $eventType;
                        }
                        if ($name !== $eventName) {
                            continue;
                        }
                        if ($namespace && $eventNamespace && $namespace !== $eventNamespace) {
                            continue;
                        }
                        foreach ($handlers as $handler) {
                            $event->data = $handler['data']
                                ?: null;
                            $params      = \array_merge([$event], $data);
                            $return      = phpQuery::callbackRun($handler['callback'], $params);
                            if ($return === false) {
                                $event->bubbles = false;
                            }
                        }
                    }
                }
                // to bubble or not to bubble...
                if (!$event->bubbles) {
                    break;
                }
                $node = $node->parentNode;
            }
        }
    }

    /**
     * Binds a handler to one or more events (like click) for each matched element.
     * Can also bind custom events.
     *
     * @param DOMNode|phpQueryObject|string $document
     * @param                               $node
     * @param                               $type
     * @param                               $data
     * @param                               $callback
     *
     * @TODO support '!' (exclusive) events
     * @TODO support more than event in $type (space-separated)
     * @TODO support binding to global events
     */
    public static function add($document, $node, $type, $data, $callback = null): void
    {
        $documentID = phpQuery::getDocumentID($document);
        $eventNode  = self::getNode($documentID, $node);
        if (!$eventNode) {
            $eventNode = self::setNode($documentID, $node);
        }
        if (!isset($eventNode->eventHandlers[$type])) {
            $eventNode->eventHandlers[$type] = [];
        }
        $eventNode->eventHandlers[$type][] = [
            'callback' => $callback,
            'data'     => $data,
        ];
    }

    /**
     * @param DOMNode|phpQueryObject|string $document
     * @param                               $node
     * @param                               $type
     * @param                               $callback
     *
     * @TODO namespace events
     * @TODO support more than event in $type (space-separated)
     */
    public static function remove($document, $node, $type = null, $callback = null): void
    {
        $documentID = phpQuery::getDocumentID($document);
        $eventNode  = self::getNode($documentID, $node);
        if (\is_object($eventNode) && isset($eventNode->eventHandlers[$type])) {
            if ($callback) {
                foreach ($eventNode->eventHandlers[$type] as $k => $handler) {
                    if ($handler['callback'] == $callback) {
                        unset($eventNode->eventHandlers[$type][$k]);
                    }
                }
            } else {
                unset($eventNode->eventHandlers[$type]);
            }
        }
    }

    /**
     * @param string $documentID
     * @param DOMElement $node
     * @return mixed
     */
    protected static function getNode($documentID, $node)
    {
        foreach (phpQuery::$documents[$documentID]->eventsNodes as $eventNode) {
            if ($node->isSameNode($eventNode)) {
                return $eventNode;
            }
        }

        return null;
    }

    /**
     * @param string $documentID
     * @param DOMElement $node
     * @return mixed
     */
    protected static function setNode($documentID, $node)
    {
        phpQuery::$documents[$documentID]->eventsNodes[] = $node;

        return phpQuery::$documents[$documentID]->eventsNodes[count(phpQuery::$documents[$documentID]->eventsNodes) - 1];
    }

    /**
     * @param string $documentID
     * @param string $type
     * @return bool
     */
    protected static function issetGlobal($documentID, $type): bool
    {
        return isset(phpQuery::$documents[$documentID])
            && \in_array($type, phpQuery::$documents[$documentID]->eventsGlobal);
    }
}
