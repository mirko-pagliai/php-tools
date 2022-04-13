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
 */
namespace Tools\Test\TestSuite\Console;

use App\Command\ExampleCommand;
use Tools\TestSuite\Console\CommandTester;
use Tools\TestSuite\TestCase;

/**
 * CommandTesterTest
 */
class CommandTesterTest extends TestCase
{
    /**
     * Tests for `assertOutputContains()` method
     * @test
     */
    public function testAssertOutputContains(): void
    {
        $CommandTester = new CommandTester(new ExampleCommand());
        $CommandTester->execute([]);
        $CommandTester->assertOutputContains('hello!');

        $this->expectAssertionFailed('The output does not contain the string `hi!`');
        $CommandTester->assertOutputContains('hi!');
    }

    /**
     * Tests for `assertOutputNotContains()` method
     * @test
     */
    public function testAssertOutputNotContains(): void
    {
        $CommandTester = new CommandTester(new ExampleCommand());
        $CommandTester->execute([]);
        $CommandTester->assertOutputNotContains('hi!');

        $this->expectAssertionFailed('The output contains the string `hello!`');
        $CommandTester->assertOutputNotContains('hello!');
    }

    /**
     * Tests for `assertCommandIsFailure()` method
     * @test
     */
    public function testAssertCommandIsFailure(): void
    {
        $CommandTester = new CommandTester(new ExampleCommand());
        $CommandTester->execute(['--failure' => true]);
        $CommandTester->assertCommandIsFailure();

        $this->expectAssertionFailed('The command did not fail');
        $CommandTester->execute([]);
        $CommandTester->assertCommandIsFailure();
    }
}
