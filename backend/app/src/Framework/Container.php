<?php

namespace App\Framework;

use RuntimeException;

/**
 * Simple IoC (Inversion of Control) container
 *
 * This lets us bind interfaces to their concrete implementations
 * in one place and inject them into controllers automatically.
 * This way controllers don't need to "new up" their own dependencies.
 */
class Container
{
    /** @var array<string, callable> */
    private array $bindings = [];

    /**
     * Register a factory for a given interface or class name
     *
     * @param string $abstract - the interface or class to bind
     * @param callable $factory - a function that returns the concrete instance
     */
    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    /**
     * Resolve and return the concrete instance for a given interface or class
     * Throws an exception if no binding has been registered
     *
     * @param string $abstract - the interface or class to resolve
     * @return object - the resolved instance
     * @throws RuntimeException - if no binding exists
     */
    public function make(string $abstract): object
    {
        if (!isset($this->bindings[$abstract])) {
            throw new RuntimeException("No binding found for: {$abstract}");
        }

        return ($this->bindings[$abstract])($this);
    }
}
