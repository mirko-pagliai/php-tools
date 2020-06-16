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
 * @since       1.4.0
 */
namespace Tools\Event;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;
use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;
use Tools\Event\Event;
use Tools\Event\EventList;

/**
 * EventDispatcher
 */
class EventDispatcher extends BaseEventDispatcher
{
    /**
     * @var \Tools\Event\EventList
     */
    protected $EventList;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (is_callable('parent::__construct')) {
            parent::__construct();
        }

        $this->EventList = new EventList();
    }

    /**
     * Dispatches an event
     * @param string|null $eventName Name of the event
     * @param object $event An `Event` instance
     * @return object The `Event` that was dispatched
     */
    public function dispatch($eventName, SymfonyEvent $event = null)
    {
        $event = $event ? $event : new Event($eventName);

        $this->getEventList()->add($event);

        return parent::dispatch($eventName, $event);
    }

    /**
     * Gets the `EventList` instance for this dispatcher
     * @return \Tools\Event\EventList
     */
    public function getEventList()
    {
        return $this->EventList;
    }
}
