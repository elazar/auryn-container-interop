<?php

namespace Elazar\Auryn\Exception;

class NotFoundException
    extends \InvalidArgumentException
    implements \Psr\Container\NotFoundExceptionInterface
{
}
