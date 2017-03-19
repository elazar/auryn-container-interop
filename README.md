# auryn-container-interop

[![Build Status](https://travis-ci.org/elazar/auryn-container-interop.svg?branch=master)](https://travis-ci.org/elazar/auryn-container-interop)

[container-interop](https://github.com/container-interop/container-interop) compatibility for [Auryn](https://github.com/rdlowrey/Auryn)

## License

This library is licensed under the [MIT License](https://opensource.org/licenses/MIT).

## Installation

Use [composer](https://getcomposer.org/).

```
composer require elazar/auryn-container-interop
```

## Usage

```php
use Elazar\Auryn\Container;
use Acme\SomeDependency;

$container = new Container;

if ($container->has(SomeDependency::class)) {
    // ...
}

$instance = $container->get(SomeDependency::class);

// All public methods of Auryn\Injector are available
$instance = $container->make(SomeDependency::class);
```

Be sure you are familiar with [how Auryn works](https://github.com/rdlowrey/Auryn#how-it-works).
As recommended by its author, avoid using it as a [service locator](https://en.wikipedia.org/wiki/Service_locator_pattern).

## Implementation

While I agree with a lot of the discussion in [this issue](https://github.com/rdlowrey/Auryn/issues/77)
regarding why new projects can use Auryn directly without a *container-interop*
implementation, I do think that such an implementation can be useful for
integrating Auryn with third-party libraries that use *container-interop*, such
as [zend-expressive](https://github.com/zendframework/zend-expressive).

The implementation in this repository takes a small amount of liberty with this
passage from [Section 1.1](https://github.com/container-interop/container-interop/blob/master/docs/ContainerInterface.md#11-basics)
of the *container-interop* specification:

> `has` ... MUST return `true` if an entry identifier is known to the container

Auryn uses [fully qualified names](https://en.wikipedia.org/wiki/Fully_qualified_name)
for classes and interfaces to identify dependencies where most container
implementations use user-designated names. As such, it's possible for Auryn to
instantiate a class even if it contains no definitions for that class (e.g.
if the class has no required constructor parameters or if those parameters are
themselves instantiable classes).

Because of this, `ContainerInterface->has()` in this *container-interop*
implementation will return `true` if either the underlying `Auryn\Injector`
instance has definitions for a requested class or interface or if a requested
class is defined and considered instantiable (i.e. is not `abstract` and has a
`public` implementation of `__construct()`). While some may view this as
technically incorrect, it seems consistent to me with the overall spirit and
intentions of the *container-interop* standard.

## Development

To run the PHPUnit test suite:

```
composer run-script test
```
