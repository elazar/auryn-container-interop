<?php

namespace Elazar\Auryn;

use Auryn\Injector;
use Elazar\Auryn\Exception\ContainerException;
use Elazar\Auryn\Exception\NotFoundException;
use Interop\Container\ContainerInterface;

class Container extends Injector implements ContainerInterface
{
    /**
     * @var array
     */
    private $has = [];

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(
                'No entry found: ' . $id
            );
        }

        try {
            return $this->make($id);
        } catch (\Exception $previous) {
            throw new ContainerException(
                'Unable to get: ' . $id,
                0,
                $previous
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        static $filter = Injector::I_BINDINGS
            | Injector::I_DELEGATES
            | Injector::I_PREPARES
            | Injector::I_ALIASES
            | Injector::I_SHARES;

        if (isset($this->has[$id])) {
            return $this->has[$id];
        }

        $definitions = array_filter($this->inspect($id, $filter));
        if (!empty($definitions)) {
            return $this->has[$id] = true;
        }

        if (!class_exists($id)) {
            return $this->has[$id] = false;
        }

        $reflector = new \ReflectionClass($id);
        if ($reflector->isInstantiable()) {
            return $this->has[$id] = true;
        }

        return $this->has[$id] = false;
    }
}
