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
namespace Tools\TestSuite;

use Tools\Event\EventDispatcher;

/**
 * Trait to assert whether events were fired or not.
 */
trait EventAssertTrait
{
    /**
     * Asserts that an event was fired
     * @param string $eventName Event name
     * @param \Tools\Event\EventDispatcher $EventDispatcher Event dispatcher
     * @param string $message The message to use for failure
     * @return void
     */
    public function assertEventFired($eventName, EventDispatcher $EventDispatcher, $message = '')
    {
        $message = $message ?: sprintf('The `%s` event was not fired', $eventName);
        $this->assertTrue($EventDispatcher->getEventList()->hasEvent($eventName), $message);
    }

    /**
     * Asserts that an event was not fired
     * @param string $eventName Event name
     * @param \Tools\Event\EventDispatcher $EventDispatcher Event dispatcher
     * @param string $message The message to use for failure
     * @return void
     */
    public function assertEventNotFired($eventName, EventDispatcher $EventDispatcher, $message = '')
    {
        $message = $message ?: sprintf('The `%s` event was not expected to be fired, instead it has been fired', $eventName);
        $this->assertFalse($EventDispatcher->getEventList()->hasEvent($eventName), $message);
    }
}
