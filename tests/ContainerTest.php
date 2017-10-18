<?php

namespace Elazar\Auryn;

use Elazar\Auryn\Exception\ContainerException;
use Elazar\Auryn\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;

class ContainerTest extends TestCase
{
    private $container;

    protected function setUp()
    {
        $this->container = new Container;
    }

    public function testHasWithDefinitions()
    {
        $this->container->alias(\Iterator::class, \ArrayIterator::class);
        $this->assertTrue($this->container->has(\Iterator::class));

        // Test caching of has() results
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
        $this->expectException(NotFoundException::class);
        $this->container->get('foo');
    }

    public function testGetWithFoundEntry()
    {
        $object = $this->container->get(\EmptyIterator::class);
        $this->assertInstanceOf(\EmptyIterator::class, $object);
    }

    public function testGetWithException()
    {
        $this->expectException(ContainerException::class);
        $this->container->get(\CallbackFilterIterator::class);
    }
}
