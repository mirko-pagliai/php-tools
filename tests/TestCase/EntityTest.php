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

namespace Tools\Test;

use App\EntityExample;
use InvalidArgumentException;
use Tools\Entity;
use Tools\TestSuite\TestCase;

/**
 * EntityTest class
 */
class EntityTest extends TestCase
{
    /**
     * @var \Tools\Entity
     */
    protected $Entity;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Entity = new EntityExample(['code' => 200]);
    }

    /**
     * Test for `__debugInfo()` method
     * @test
     */
    public function testDebugInfo(): void
    {
        ob_start();
        $line = __LINE__ + 1;
        debug($this->Entity);
        $dump = ob_get_clean() ?: '';
        $assertStringContainsString = function (string $first, string $second) {
            if (is_callable([$this, 'assertStringContainsString'])) {
                $method = [$this, 'assertStringContainsString'];
            }
            call_user_func($method ?? 'self::assertContains', $first, $second);
        };
        $assertStringContainsString(EntityExample::class, $dump);

        $this->skipIf(IS_WIN);
        $assertStringContainsString(__FILE__ . ' (line ' . $line . ')', $dump);
        $assertStringContainsString('########## DEBUG ##########', $dump);
        $assertStringContainsString('App\EntityExample {', $dump);
    }

    /**
     * Test for `has()` method
     * @test
     */
    public function testHas(): void
    {
        $this->assertTrue($this->Entity->has('code'));
        $this->assertFalse($this->Entity->has('noExisting'));

        $this->assertTrue($this->Entity->set('keyWithNull', null)->has('keyWithNull'));
        $this->assertTrue($this->Entity->set('keyWithEmptyValue', '')->has('keyWithEmptyValue'));
    }

    /**
     * Test for `__get()` and `get()` methods
     * @test
     */
    public function testGet(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertSame(200, $this->Entity->code);
        $this->assertSame(200, $this->Entity->get('code'));
        /** @phpstan-ignore-next-line */
        $this->assertNull($this->Entity->noExisting);
        $this->assertNull($this->Entity->get('noExisting'));
        $this->assertSame('default', $this->Entity->get('noExisting', 'default'));
    }

    /**
     * Test for `set()` method
     * @test
     */
    public function testSet(): void
    {
        $result = $this->Entity->set('newKey', 'newValue');
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertSame('newValue', $this->Entity->get('newKey'));

        $result = $this->Entity->set(['alfa' => 'first', 'beta' => 'second']);
        $this->assertSame('first', $this->Entity->get('alfa'));
        $this->assertSame('second', $this->Entity->get('beta'));

        $this->assertNull($this->Entity->set('keyWithNull', null)->get('keyWithNull'));
        $this->assertSame('', $this->Entity->set('keyWithEmptyValue', '')->get('keyWithEmptyValue'));
    }

    /**
     * Test for `toArray()` method
     * @test
     */
    public function testToArray(): void
    {
        $expected = ['code' => 200, 'newKey' => 'newValue'];
        $result = $this->Entity->set('newKey', 'newValue')->toArray();
        $this->assertSame($expected, $result);

        $expected += ['subEntity' => ['subKey' => 'subValue']];
        $subEntity = new EntityExample(['subKey' => 'subValue']);
        $result = $this->Entity->set(compact('subEntity'))->toArray();
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `ArrayAccess` interface, so for `offsetExists()`, `offsetGet()`,
     *  `offsetSet()` and `offsetUnset()` methods
     * @test
     */
    public function testArrayAccess(): void
    {
        $this->Entity['newKey'] = 'a key';
        $this->assertTrue(isset($this->Entity['newKey']));
        $this->assertSame('a key', $this->Entity['newKey']);
        unset($this->Entity['newKey']);
        $this->assertFalse(isset($this->Entity['newKey']));
    }
}
