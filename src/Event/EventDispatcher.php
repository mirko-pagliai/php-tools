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

use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;
use Tools\Event\EventList;

/**
 * EventDispatcher.
 *
 * Listeners are registered on the manager and events are dispatched through the
 * manager.
 */
class EventDispatcher extends BaseEventDispatcher
{
    /**
     * @var \Tools\Event\EventList
     */
    protected EventList $EventList;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->EventList = new EventList();
    }

    /**
     * Dispatches an event
     * @param object $event An `Event` instance
     * @param string|null $eventName Name of the event
     * @return object The `Event` that was dispatched
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        /** @var \Tools\Event\Event $event */
        $this->getEventList()->add($event);

        return parent::dispatch($event, $eventName);
    }

    /**
     * Gets the `EventList` instance for this dispatcher
     * @return \Tools\Event\EventList
     */
    public function getEventList(): EventList
    {
        return $this->EventList;
    }
}
