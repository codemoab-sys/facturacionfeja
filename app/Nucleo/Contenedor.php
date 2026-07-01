<?php
declare(strict_types=1);

namespace App\Nucleo;

use Closure;
use RuntimeException;

class Contenedor
{
    private static ?Contenedor $instance = null;
    private array $bindings = [];
    private array $singletons = [];
    private array $instances = [];

    final private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function bind(string $abstract, callable|object|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, callable|object|string $concrete): void
    {
        $this->singletons[$abstract] = $concrete;
    }

    public function make(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->singletons[$abstract])) {
            $concrete = $this->singletons[$abstract];
            $object = $this->resolve($concrete);
            $this->instances[$abstract] = $object;
            return $object;
        }

        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract];
            return $this->resolve($concrete);
        }

        if (class_exists($abstract)) {
            return new $abstract();
        }

        throw new RuntimeException("No binding found for: {$abstract}");
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract])
            || isset($this->singletons[$abstract])
            || isset($this->instances[$abstract]);
    }

    private function resolve(callable|object|string $concrete): object
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }
        if (is_object($concrete)) {
            return $concrete;
        }
        if (is_string($concrete) && class_exists($concrete)) {
            return new $concrete();
        }
        throw new RuntimeException("Cannot resolve binding");
    }
}
