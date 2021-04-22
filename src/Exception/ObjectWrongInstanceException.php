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
 * @since       1.4.7
 */

namespace Tools\Exception;

use Exception;
use Throwable;

/**
 * "Object wrong instance" exception.
 */
class ObjectWrongInstanceException extends Exception
{
    /**
     * Object instance
     * @var object
     */
    protected $object;

    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param object|null $object Object that is not a right instance
     */
    public function __construct(?string $message = null, int $code = 0, ?Throwable $previous = null, ?object $object = null)
    {
        if (!$message) {
            $message = $object ? sprintf('Object `%s` is not a right instance', get_class($object)) : 'Object is not a right instance';
        }
        parent::__construct($message, $code, $previous);
        $this->object = $object;
    }

    /**
     * Gets the object that is a wrong instance
     * @return object|null
     */
    public function getObject(): ?object
    {
        return $this->object;
    }
}
