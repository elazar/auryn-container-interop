<?php

namespace Elazar\Auryn\Exception;

class NotFoundException
    extends \InvalidArgumentException
    implements \Interop\Container\Exception\NotFoundException
{
}
