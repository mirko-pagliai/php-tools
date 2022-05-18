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
    public function assertEventFired(string $eventName, EventDispatcher $EventDispatcher, string $message = ''): void
    {
        $this->assertTrue($EventDispatcher->getEventList()->hasEvent($eventName), $message ?: 'The `' . $eventName . '` event was not fired');
    }

    /**
     * Asserts that an event was fired, with specified arguments
     * @param string $eventName Event name
     * @param array $eventArgs Event arguments
     * @param \Tools\Event\EventDispatcher $EventDispatcher Event dispatcher
     * @param string $message The message to use for failure
     * @return void
     * @since 1.5.10
     */
    public function assertEventFiredWithArgs(string $eventName, array $eventArgs, EventDispatcher $EventDispatcher, string $message = ''): void
    {
        $this->assertEventFired($eventName, $EventDispatcher, $message);

        $matching = false;
        foreach ($EventDispatcher->getEventList()->toArray() as $event) {
            if ($event->getName() === $eventName && $event->getArgs() === $eventArgs) {
                $matching = true;
            }
        }
        if (!$message) {
            $message = 'The `' . $eventName . '` event was not fired with the specified arguments';
            if (count($eventArgs) === 1 && is_string(array_value_first($eventArgs))) {
                $message .= ' `' . array_value_first($eventArgs) . '`';
            }
        }
        $this->assertTrue($matching, $message);
    }

    /**
     * Asserts that an event was not fired
     * @param string $eventName Event name
     * @param \Tools\Event\EventDispatcher $EventDispatcher Event dispatcher
     * @param string $message The message to use for failure
     * @return void
     */
    public function assertEventNotFired(string $eventName, EventDispatcher $EventDispatcher, string $message = ''): void
    {
        $this->assertFalse($EventDispatcher->getEventList()->hasEvent($eventName), $message ?: 'The `' . $eventName . '` event was not expected to be fired, instead it has been fired');
    }
}
