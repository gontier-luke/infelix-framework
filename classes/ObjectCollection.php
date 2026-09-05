<?php

namespace Classes\Collections;  

class ObjectCollection implements \Countable
{
    protected string $type;

    /** @var object[] */
    public array $objects = [];

    public function add(object $object): void
    {
        $this->objects[] = $object;
    }

    /** @return object[] */
    public function getAll(): array
    {
        return $this->objects;
    }

    public function get(int $index): object
    {
        return $this->objects[$index] ?? null;
    }

    public function remove(int $index): void
    {
        unset($this->objects[$index]);
    }

    public function count(): int
    {
        return count($this->objects);
    }

    public function clear(): void
    {
        $this->objects = [];
    }

    public function contains(object $object): bool
    {
        return in_array($object, $this->objects);
    }

    public function indexOf(object $object): int
    {
        return array_search($object, $this->objects);
    }

    public function isEmpty(): bool
    {
        return empty($this->objects);
    }

    public function sort(callable $comparator): self
    {
        usort($this->objects, $comparator);
        return $this;
    }

    public function reverse(): void
    {
        $this->objects = array_reverse($this->objects);
    }

    public function filter(callable $callback): self
    {
        $filtered = new self($this->type);
        foreach ($this->objects as $object) {
            if ($callback($object)) {
                $filtered->add($object);
            }
        }
        return $filtered;
    }

    /**
     * Applique une fonction de rappel à chaque élément de la collection et retourne un nouvel Array contenant les résultats.
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback, mixed ...$extraVar): array
    {
        $mapped = [];
        foreach ($this->objects as $object) {
            $mapped[] = $callback($object, ...$extraVar);
        }
        return $mapped;
    }

    public function forEach(callable $callback): void
    {
        foreach ($this->objects as $object) {
            $callback($object);
        }
    }

    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->objects, $callback, $initial);
    }

    public function toArray(): array
    {
        return $this->objects;
    }

    public function __toString(): string
    {
        return json_encode($this->objects);
    }

    public function __clone()
    {
        $this->objects = array_map(function ($object) {
            return clone $object;
        }, $this->objects);
    }

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function __destruct()
    {
        $this->objects = [];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function first(): ?object
    {
        return $this->get(0) ?? null;
    }
    
} 