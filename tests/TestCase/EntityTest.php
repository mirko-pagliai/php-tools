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
    public function testDebugInfo()
    {
        ob_start();
        $line = __LINE__ + 1;
        debug($this->Entity);
        $dump = ob_get_clean();
        $assertStringContainsString = function () {
            $method = 'assertContains';
            if (method_exists($this, 'assertStringContainsString')) {
                $method = 'assertStringContainsString';
            }
            call_user_func_array([$this, $method], func_get_args());
        };
        $assertStringContainsString(EntityExample::class, $dump);

        $this->skipIf(IS_WIN);
        $assertStringContainsString(__FILE__ . ' (line ' . $line . ')', $dump);
        $assertStringContainsString('########## DEBUG ##########', $dump);
        $assertStringContainsString('App\EntityExample {', $dump);
        $assertStringContainsString('code: 200', $dump);
    }

    /**
     * Test for `has()` method
     * @test
     */
    public function testHas()
    {
        $this->assertTrue($this->Entity->has('code'));
        $this->assertFalse($this->Entity->has('noExisting'));
    }

    /**
     * Test for `__get()` and `get()` methods
     * @test
     */
    public function testGet()
    {
        $this->assertSame(200, $this->Entity->code);
        $this->assertSame(200, $this->Entity->get('code'));
        $this->assertNull($this->Entity->noExisting);
        $this->assertNull($this->Entity->get('noExisting'));
        $this->assertSame('default', $this->Entity->get('noExisting', 'default'));
    }

    /**
     * Test for `set()` method
     * @test
     */
    public function testSet()
    {
        $result = $this->Entity->set('newKey', 'newValue');
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertSame('newValue', $this->Entity->get('newKey'));

        $result = $this->Entity->set(['alfa' => 'first', 'beta' => 'second']);
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertSame('first', $this->Entity->get('alfa'));
        $this->assertSame('second', $this->Entity->get('beta'));
    }

    /**
     * Test for `toArray()` method
     * @test
     */
    public function testToArray()
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
    public function testArrayAccess()
    {
        $this->Entity['newKey'] = 'a key';
        $this->assertTrue(isset($this->Entity['newKey']));
        $this->assertSame('a key', $this->Entity['newKey']);
        unset($this->Entity['newKey']);
        $this->assertFalse(isset($this->Entity['newKey']));
    }
}
