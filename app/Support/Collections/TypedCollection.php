<?php

namespace App\Support\Collections;

use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * @template T
 * @extends Collection<int, T>
 */
abstract class TypedCollection extends Collection
{
    /**
     * Get the expected type for this collection
     */
    abstract protected function getExpectedType(): string;

    /**
     * Get a human-readable name for the collection
     */
    abstract protected function getCollectionName(): string;

    public static function from(iterable $items): static
    {
        return new static($items);
    }

    /**
     * Validate that the item is of the expected type
     */
    protected function validateType($item): void
    {
        $expectedType = $this->getExpectedType();

        if (!$item instanceof $expectedType) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s can only contain %s objects, %s given',
                    $this->getCollectionName(),
                    class_basename($expectedType),
                    get_debug_type($item)
                )
            );
        }
    }

    // Surcharge des méthodes d'ajout
    public function push(...$values): static
    {
        foreach ($values as $value) {
            $this->validateType($value);
        }

        return parent::push(...$values);
    }

    public function add($item): static
    {
        $this->validateType($item);
        return parent::add($item);
    }

    public function put($key, $value): static
    {
        $this->validateType($value);
        return parent::put($key, $value);
    }

    public function prepend($value, $key = null): static
    {
        $this->validateType($value);
        return parent::prepend($value, $key);
    }

    public function __construct($items = [])
    {
        $validatedItems = [];

        foreach ($this->getArrayableItems($items) as $key => $item) {
            $this->validateType($item);
            $validatedItems[$key] = $item;
        }

        parent::__construct($validatedItems);
    }

    /**
     * Override filter to return same collection type
     */
    public function filter(callable $callback = null): static
    {
        return parent::filter($callback);
    }

    /**
     * Override map but return generic Collection (since result type is unknown)
     */
    public function map(callable $callback): Collection
    {
        return new Collection(array_map($callback, $this->items));
    }
}
