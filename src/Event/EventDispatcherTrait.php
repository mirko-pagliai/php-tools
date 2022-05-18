<?php
declare(strict_types=1);

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
 * @since       1.4.0
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
    protected EventDispatcher $EventDispatcher;

    /**
     * Gets the `EventDispatcher`
     * @return \Tools\Event\EventDispatcher
     */
    public function getEventDispatcher(): EventDispatcher
    {
        return $this->EventDispatcher ??= new EventDispatcher();
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
     * @param mixed $args Any event argument you wish to be transported with
     *  this event to it can be read by listeners
     * @return \Tools\Event\Event
     */
    public function dispatchEvent(string $name, $args = null): Event
    {
        $event = new Event($name, $args);
        $this->getEventDispatcher()->dispatch($event, $name);

        return $event;
    }
}
