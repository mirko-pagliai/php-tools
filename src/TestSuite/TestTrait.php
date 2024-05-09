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
 * @since       1.0.2
 */

namespace Tools\TestSuite;

use BadMethodCallException;

/**
 * A trait that provides some assertion methods.
 * @method static void assertIsArray(mixed $var, ?string $message = '') Asserts that `$var` is an array
 * @method static void assertIsBool(mixed $var, ?string $message = '') Asserts that `$var` is a boolean
 * @method static void assertIsCallable(mixed $var, ?string $message = '') Asserts that `$var` is a callable
 * @method static void assertIsFloat(mixed $var, ?string $message = '') Asserts that `$var` is a float
 * @method static void assertIsInt(mixed $var, ?string $message = '') Asserts that `$var` is an int
 * @method static void assertIsIterable(mixed $var, ?string $message = '') Asserts that `$var` is iterable, i.e. that it is an array or an object implementing `Traversable`
 * @method static void assertIsJson(mixed $var, ?string $message = '') Asserts that `$var` is a json string
 * @method static void assertIsObject(mixed $var, ?string $message = '') Asserts that `$var` is an object
 * @method static void assertIsPositive(mixed $var, ?string $message = '') Asserts that `$var` is a positive number
 * @method static void assertIsResource(mixed $var, ?string $message = '') Asserts that `$var` is a resource
 * @method static void assertIsString(mixed $var, ?string $message = '') Asserts that `$var` is a string
 * @method static void assertIsUrl(mixed $var, ?string $message = '') Asserts that `$var` is an url
 */
trait TestTrait
{
    /**
     * Magic `__call()` method.
     *
     * Provides some `assertIs*()` methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param array $arguments Arguments
     * @return void
     * @since 1.1.12
     */
    public function __call(string $name, array $arguments): void
    {
        self::__callStatic($name, $arguments);
    }

    /**
     * Magic `__callStatic()` method.
     *
     * Provides some `assertIs*()` methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param array $arguments Arguments
     * @return void
     * @since 1.1.12
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $name, array $arguments): void
    {
        if (str_starts_with($name, 'assertIs')) {
            $count = count($arguments);
            if (!$count || $count > 2) {
                throw new BadMethodCallException(sprintf('Method %s::%s() expects at least 1 argument, maximum 2, %d passed', __CLASS__, $name, $count));
            }

            /** @var callable $function */
            $function = match ($name) {
                'assertIsJson' => 'json_validate',
                default => 'is_' . strtolower(substr($name, 8)),
            };

            if (is_callable($function)) {
                $var = array_shift($arguments);
                /** @var callable $callable */
                $callable = [__CLASS__, 'assertTrue'];
                call_user_func_array($callable, [$function($var), ...array_values($arguments)]);

                return;
            }
        }

        throw new BadMethodCallException(sprintf('Method %s::%s() does not exist', __CLASS__, $name));
    }

    /**
     * Skips the test if the condition is `true`
     * @param bool $shouldSkip Whether the test should be skipped
     * @param string $message The message to display
     * @return bool
     */
    public function skipIf(bool $shouldSkip, string $message = ''): bool
    {
        if ($shouldSkip) {
            self::markTestSkipped($message); // @codeCoverageIgnore
        }

        return $shouldSkip;
    }
}
