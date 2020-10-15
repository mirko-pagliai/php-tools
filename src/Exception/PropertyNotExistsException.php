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
 * @since       1.1.14
 */

namespace Tools\Exception;

use Exception;

/**
 * "Property does not exist" exception.
 */
class PropertyNotExistsException extends Exception
{
    /**
     * Property name
     * @var string|null
     */
    protected $property;

    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param string|null $property Name of the property that do not exist
     */
    public function __construct($message = null, $code = 0, \Throwable $previous = null, $property = null)
    {
        if (!$message) {
            $message = $property ? sprintf('Property `%s` does not exist', $property) : 'Property does not exist';
        }
        parent::__construct($message, $code, $previous);
        $this->property = $property;
    }

    /**
     * Gets the name of the property that do not exist
     * @return string|null
     * @since 1.2.11
     */
    public function getPropertyName()
    {
        return $this->property;
    }
}
