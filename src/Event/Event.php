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

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Tools\Exceptionist;

/**
 * Event instance.
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
     * @param mixed $args Any event argument you wish to be transported with
     *  this event to it can be read by listeners
     */
    public function __construct($name, $args = null)
    {
        $this->name = $name;
        $this->args = (array)$args;
    }

    /**
     * Gets the argument with the specified index of this event
     * @param int $index Index
     * @return mixed
     * @throws \Tools\Exception\KeyNotExistsException
     */
    public function getArg($index)
    {
        Exceptionist::arrayKeyExists($index, $this->args, sprintf('Argument with index `%s` does not exist', $index));

        return $this->args[$index];
    }

    /**
     * Gets the arguments of this event
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Gets the name of the event
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
