<?php

namespace Elazar\Auryn;

use Auryn\Injector;
use Auryn\InjectorException;
use Auryn\Reflector;
use Elazar\Auryn\Exception\ContainerException;
use Elazar\Auryn\Exception\NotFoundException;
use Interop\Container\ContainerInterface;

class Container extends Injector implements ContainerInterface
{
	/**
     * @var ContainerInterface[]
     */
    private $containerDelegates = [];

    /**
     * @var array
     */
    private $containerCache = [];

    /**
     * @var array
     */
    private $delegateCache = [];

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if ($this->containerHas($id)) {
            return $this->containerGet($id);
        }

        $delegate = $this->delegateWith($id);
        if ($delegate instanceof ContainerInterface) {
            return $delegate->get($id);
        }

        throw new NotFoundException(
            'No entry found: ' . $id
        );
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        return $this->containerHas($id)
            || $this->delegateWith($id) instanceof ContainerInterface;
    }

    /**
     * @param ContainerInterface $delegate
     */
    public function addDelegate(ContainerInterface $delegate)
    {
        $this->containerDelegates[] = $delegate;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ContainerException
     */
    private function containerGet($id)
    {
        try {
            return $this->make($id);
        } catch (InjectorException $previous) {
            throw new ContainerException(
                'Unable to get: ' . $id,
                0,
                $previous
            );
        }
    }

    /**
     * @param string $id
     * @return boolean
     */
    private function containerHas($id)
    {
        static $filter = Injector::I_BINDINGS
            | Injector::I_DELEGATES
            | Injector::I_PREPARES
            | Injector::I_ALIASES
            | Injector::I_SHARES;

        if (isset($this->containerCache[$id])) {
            return $this->containerCache[$id];
        }

        $definitions = array_filter($this->inspect($id, $filter));
        if (!empty($definitions)) {
            return $this->containerCache[$id] = true;
        }

        if (!class_exists($id)) {
            return $this->containerCache[$id] = false;
        }

        $reflector = new \ReflectionClass($id);
        if ($reflector->isInstantiable()) {
            return $this->containerCache[$id] = true;
        }

        return $this->containerCache[$id] = false;
    }

    /**
     * @param string $id
     * @return ContainerInterface|null
     */
    private function delegateWith($id)
    {
        if (isset($this->delegateCache[$id])) {
            return $this->delegateCache[$id];
        }

        foreach ($this->containerDelegates as $delegate) {
            if ($delegate->has($id)) {
                return $this->delegateCache[$id] = $delegate;
            }
        }

        return null;
    }
}
