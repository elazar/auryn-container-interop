<?php

namespace Elazar\Auryn;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;

class ContainerTest extends TestCase
{
    protected function setUp()
    {
        $this->container = new Container;
    }

    public function testHasWithDefinitions()
    {
        $this->container->alias(\Iterator::class, \ArrayIterator::class);
        $this->assertTrue($this->container->has(\Iterator::class));
    }

    public function testHasWithNonexistentClass()
    {
        $this->assertFalse($this->container->has('foo'));
    }

    public function testHasWithUninstantiableClass()
    {
        $this->assertFalse($this->container->has(\FilterIterator::class));
    }

    public function testHasWithInstantiableClass()
    {
        $this->assertTrue($this->container->has(\ArrayIterator::class));
    }

    public function testGetWithMissingEntry()
    {
        $this->expectException(Exception\NotFoundException::class);
        $this->container->get('foo');
    }

    public function testGetWithFoundEntry()
    {
        $object = $this->container->get(\stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $object);
    }

    public function testAddDelegate()
    {
        $delegate = $this->createMock(ContainerInterface::class);

        $delegate
            ->expects($this->any())
            ->method('has')
            ->with(\Iterator::class)
            ->willReturn(true);

        $instance = new \ArrayIterator([]);
        $delegate
            ->expects($this->any())
            ->method('get')
            ->with(\Iterator::class)
            ->willReturn($instance);

        $this->container->addDelegate($delegate);

        $this->assertTrue($this->container->has(\Iterator::class));
        $this->assertSame($instance, $this->container->get(\Iterator::class));
    }
}
