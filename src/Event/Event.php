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

use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

/**
 * Event
 */
class Event extends BaseEvent
{
    /**
     * Custom data for the method that receives the event
     * @var array
     */
    protected $args;

    /**
     * Name of the event
     * @var string
     */
    protected $name;

    /**
     * Construct
     * @param string $name Name of the event
     * @param array|\ArrayAccess|null $args Any event argument you wish to be
     *  transported with this event to it can be read by listeners
     */
    public function __construct(string $name, $args = null)
    {
        $this->name = $name;
        $this->args = (array)$args;
    }

    /**
     * Gets the arguments of this event
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Gets the name of the event
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
