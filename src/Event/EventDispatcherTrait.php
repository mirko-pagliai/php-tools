<?php
/**
 * This file is part of php-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @since       1.3.5
 */
namespace Tools\Event;

use Tools\Event\Event;
use Tools\Event\EventDispatcher;

/**
 * EventDispatcherTrait.
 *
 * A trait that allows you to quickly access the dispatcher and dispatch an event.
 */
trait EventDispatcherTrait
{
    /**
     * @var \Tools\Event\EventDispatcher
     */
    protected $EventDispatcher;

    /**
     * Gets the `EventDispatcher`
     * @return \Tools\Event\EventDispatcher
     */
    public function getEventDispatcher()
    {
        if (!$this->EventDispatcher) {
            $this->EventDispatcher = new EventDispatcher();
        }

        return $this->EventDispatcher;
    }

    /**
     * Sets a new `EventDispatcher`
     * @param \Tools\Event\EventDispatcher $eventDispatcher Event dispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->EventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Dispatches an event
     * @param string $name Name of the event
     * @param array|\ArrayAccess|null $args Any event argument you wish to be
     *  transported with this event to it can be read by listeners
     * @return \Tools\Event\Event
     */
    public function dispatchEvent($name, $args = null)
    {
        $event = new Event($name, $args);
        $this->getEventDispatcher()->dispatch($name, $event);

        return $event;
    }
}
