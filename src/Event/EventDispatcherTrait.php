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

/**
 * EventDispatcherTrait.
 *
 * A trait that allows you to quickly access the dispatcher and dispatch an event.
 * @deprecated The `EventDispatcherTrait` class is deprecated and will be removed in a future release
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
        trigger_deprecation('php-tools', '1.9.5', 'The `EventDispatcherTrait` class is deprecated and will be removed in a future release');

        return $this->EventDispatcher ??= new EventDispatcher();
    }

    /**
     * Sets a new `EventDispatcher`
     * @param \Tools\Event\EventDispatcher $eventDispatcher Event dispatcher
     * @return $this
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        trigger_deprecation('php-tools', '1.9.5', 'The `EventDispatcherTrait` class is deprecated and will be removed in a future release');

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
    public function dispatchEvent(string $name, mixed $args = null): Event
    {
        trigger_deprecation('php-tools', '1.9.5', 'The `EventDispatcherTrait` class is deprecated and will be removed in a future release');

        $event = new Event($name, $args);
        $this->getEventDispatcher()->dispatch($event, $name);

        return $event;
    }
}
