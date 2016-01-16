<?php

namespace idfly\moka;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Create object with method 'METHOD' that returns 'RESULT'
     */
    public function testStubReturnsResult()
    {
        $stub = \idfly\Moka::stub(null, ['method' => 'RESULT']);
        $this->assertEquals('RESULT', $stub->method());
    }

    /**
     * Redefine result on mock
     */
    public function testStubReturnsAssignedResult()
    {
        $stub = \idfly\Moka::stub(null, ['method' => 'OLD']);
        $stub->moka()->stubs('method')->returns('NEW');
        $this->assertEquals('NEW', $stub->method());
    }


    /**
     * Set specific return value for argument
     */
    public function testStubReturnsResultByArgument()
    {
        $stub = \idfly\Moka::stub(null, ['method' => 'DEFAULT']);
        $stub->moka()->stubs('method')->with('ARG')->returns('RESULT');
        $this->assertEquals('RESULT', $stub->method('ARG'));
    }

    /**
     * Set specific return value for callback
     */
    public function testStubReturnsResultByCallack()
    {
        $stub = \idfly\Moka::stub(null, ['method' => 'DEFAULT']);
        $callback = function () {
            return true;
        };
        $stub->moka()->stubs('method')->on($callback)->returns('RESULT');
        $this->assertEquals('RESULT', $stub->method('ARG'));
    }

    /**
     * Set specific return value for second call
     */
    public function testStubReturnsResultInOrder()
    {
        $stub = \idfly\Moka::stub(null, ['method' => 'DEFAULT']);
        $stub->moka()->stubs('method')->at(1)->returns('RESULT');
        $stub->method(); // DEFAULT
        $this->assertEquals('RESULT', $stub->method());
    }

    /**
     * Set callback for stub
     */
    public function testStubReturnsResultOfCallack()
    {
        $stub = \idfly\Moka::stub(null, ['method' => 'DEFAULT']);
        $callback = function () {
            return 'RESULT';
        };

        $stub->moka()->stubs('method')->calls($callback);
        $this->assertEquals('RESULT', $stub->method('ARG'));
    }

    /**
     * Check method call count
     */
    public function testStubReturnsMethodCallCount()
    {
        $stub = \idfly\Moka::stub(null, ['method' => 'RESULT']);
        $stub->method();
        $this->assertEquals(1, sizeof($stub->moka()->report('method')));
    }

    /**
     * Check method arguments
     */
    public function testStubReturnsMethodCallArgs()
    {
        $stub = \idfly\Moka::stub(null, ['method' => 'RESULT']);
        $stub->method('ARG');
        $this->assertEquals(['ARG'], $stub->moka()->report('method')[0]);
    }

    /**
     * Stub static method
     */
    public function testStubsClass()
    {
        $class = \idfly\Moka::stubClass(null, ['::method' => 'RESULT']);
        $this->assertEquals('RESULT', $class::method());
    }

    /**
     * Apply arguments checking on class
     */
    public function testStubClassMethodWithArguments()
    {
        $class = \idfly\Moka::stubClass(null, ['::method' => 'DEFAULT']);
        $class::$moka->stubs('method')->with('ARG')->returns('RESULT');
        $this->assertEquals('RESULT', $class::method('ARG'));
    }

    /**
     * Stub only some methods of class and leave another unchanged:
     */
    public function testMockClass()
    {
        $class = \idfly\Moka::mockClass('\idfly\Moka', ['::method' => 'VALUE']);
        $this->assertEquals('VALUE', $class::method());
        $this->assertInstanceOf('\idfly\moka\Spy', $class::spy());
    }

    /**
     * Report static calls arguments
     */
    public function testReportStaticCalls()
    {
        $class = \idfly\Moka::stubClass(null, ['::method' => 'RESULT']);
        $class::method('ARG');
        $this->assertEquals(['ARG'], $class::$moka->report('method')[0]);
    }

    /**
     * Report class instance arguments
     */
    public function testReturnsInstance()
    {
        $class = \idfly\Moka::stubClass(null, ['call' => 'RESULT']);
        (new $class())->call('ARG');
        $instance = $class::$moka->instance(0);
        $this->assertEquals(['ARG'], $instance->moka()->report('call')[0]);
    }

    /**
     * Report constructor argument
     */
    public function testStubClassTracksConstructorArguments()
    {
        $class = \idfly\Moka::stubClass(null, ['__construct' => null]);
        new $class('ARG1', 'ARG2');
        $report = $class::$moka->instance(0)->moka()->report('__construct');
        $this->assertEquals(['ARG1', 'ARG2'], $report[0]);
    }

    const HELPER = '\idfly\moka\IntegrationTestHelper';

    /**
     * Create stub with parent
     */
    public function testStubParent()
    {
        $stub = \idfly\Moka::stub(self::HELPER, ['method']);
        $this->assertInstanceOf(self::HELPER, $stub);
    }

    /**
     * Create stub with parent
     */
    public function testOverridesParentValue()
    {
        $stub = \idfly\Moka::stub(self::HELPER, ['method' => 'VALUE']);
        $this->assertEquals('VALUE', $stub->method());
    }

    public function testStubClassRemovesMethodFromParent()
    {
        $this->setExpectedException(
            '\idfly\moka\Error',
            'method "method" is not stubbed'
        );

        $stub = \idfly\Moka::stub(self::HELPER, []);
        $stub->method();
    }

    public function testStubClassAttachesExtraMethodToParent()
    {
        $stub = \idfly\Moka::stub(self::HELPER, ['extra' => 'EXTRA']);
        $this->assertEquals('EXTRA', $stub->extra());
    }

    /**
     * Create test spy (similar as in sinon js, but with less methods)
     */
    public function testSpyReturnsCallable()
    {
        $spy = \idfly\Moka::spy('RESULT');
        $this->assertEquals('RESULT', $spy());
    }

    /**
     * Adjust spy behaviour
     */
    public function testSpyHasReport()
    {
        $spy = \idfly\Moka::spy('DEFAULT');
        $spy->stubs()->with('ARG')->returns('RESULT');
        $this->assertEquals('RESULT', $spy('ARG'));
    }
}


class IntegrationTestHelper
{

    public function method()
    {
        return 'RESULT';
    }
}
