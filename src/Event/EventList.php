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

use ArrayAccess;

/**
 * EventList.
 *
 * It allows you to manage the events already dispatched.
 */
class EventList implements ArrayAccess
{
    /**
     * Events list
     * @var array<\Tools\Event\Event>
     */
    protected array $_events = [];

    /**
     * Empties the list of dispatched events
     * @return void
     */
    public function flush(): void
    {
        $this->_events = [];
    }

    /**
     * Adds an event to the list when event listing is enabled
     * @param \Tools\Event\Event $event An event to the list of dispatched events.
     * @return void
     */
    public function add(Event $event): void
    {
        $this->_events[] = $event;
    }

    /**
     * Whether an offset exists
     * @link https://secure.php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset An offset to check for
     * @return bool True on success or false on failure
     */
    public function offsetExists($offset): bool
    {
        return isset($this->_events[$offset]);
    }

    /**
     * Offset to retrieve
     * @link https://secure.php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset The offset to retrieve
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->_events[$offset] : null;
    }

    /**
     * Offset to set
     * @link https://secure.php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset The offset to assign the value to
     * @param mixed $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->_events[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link https://secure.php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->_events[$offset]);
    }

    /**
     * Counts events
     * @return int
     */
    public function count(): int
    {
        return count($this->_events);
    }

    /**
     * Extracts events by name
     * @param string $name Event name
     * @return array<\Tools\Event\Event>
     * @since 1.5.12
     */
    public function extract(string $name): array
    {
        return array_values(array_filter($this->_events, fn($event) => $event->getName() === $name));
    }

    /**
     * Checks if an event is in the list
     * @param string $name Event name
     * @return bool
     */
    public function hasEvent(string $name): bool
    {
        return !empty($this->extract($name));
    }

    /**
     * Returns the `EventList` as array
     * @return array<\Tools\Event\Event>
     * @since 1.4.1
     */
    public function toArray(): array
    {
        return $this->_events;
    }
}
