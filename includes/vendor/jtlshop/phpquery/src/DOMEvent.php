<?php declare(strict_types=1);

namespace JTL\phpQuery;

/**
 * DOMEvent class.
 *
 * Based on
 *
 * @link http://developer.mozilla.org/En/DOM:event
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 * @package phpQuery
 * @todo implement ArrayAccess ?
 */
class DOMEvent
{
    /**
     * Returns a boolean indicating whether the event bubbles up through the DOM or not.
     *
     * @var bool
     */
    public bool $bubbles = true;

    /**
     * Returns a reference to the currently registered target for the event.
     *
     * @var
     */
    public $currentTarget;

    /**
     * Returns detail about the event, depending on the type of event.
     *
     * @var
     * @link http://developer.mozilla.org/en/DOM/event.detail
     */
    public $detail;    // ???

    /**
     * Used to indicate which phase of the event flow is currently being evaluated.
     *
     * NOT IMPLEMENTED
     *
     * @var
     * @link http://developer.mozilla.org/en/DOM/event.eventPhase
     */
    public $eventPhase;    // ???

    /**
     * The explicit original target of the event (Mozilla-specific).
     *
     * NOT IMPLEMENTED
     *
     * @var
     */
    public $explicitOriginalTarget; // moz only

    /**
     * The original target of the event, before any retargetings (Mozilla-specific).
     *
     * NOT IMPLEMENTED
     *
     * @var
     */
    public $originalTarget;    // moz only

    /**
     * Identifies a secondary target for the event.
     *
     * @var
     */
    public $relatedTarget;

    /**
     * Returns a reference to the target to which the event was originally dispatched.
     *
     * @var
     */
    public $target;

    /**
     * Returns the time that the event was created.
     *
     * @var string
     */
    public $timeStamp;

    /**
     * @var string
     */
    public $type;

    /**
     * @var bool
     */
    public $runDefault = true;

    /**
     * @var null
     */
    public $data;

    /**
     * DOMEvent constructor.
     * @param array $data
     */
    public function __construct($data)
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
        if (!$this->timeStamp) {
            $this->timeStamp = \time();
        }
    }

    /**
     * Cancels the event (if it is cancelable).
     */
    public function preventDefault(): void
    {
        $this->runDefault = false;
    }

    /**
     * Stops the propagation of events further along in the DOM.
     */
    public function stopPropagation(): void
    {
        $this->bubbles = false;
    }
}
