<?php

use App\Support\Collections\TypedCollection;

class StubTypedCollection extends TypedCollection
{
    protected function getExpectedType(): string
    {
        return \stdClass::class;
    }

    protected function getCollectionName(): string
    {
        return 'StubCollection';
    }
}

describe('TypedCollection', function () {
    test('it can be instantiated with valid items', function () {
        $items = [new \stdClass(), new \stdClass()];
        $collection = new StubTypedCollection($items);

        expect($collection)->toHaveCount(2);
    });

    test('it throws exception on invalid item type during instantiation', function () {
        expect(fn() => new StubTypedCollection([new \stdClass(), 123]))
            ->toThrow(InvalidArgumentException::class, 'StubCollection can only contain stdClass objects, int given');
    });

    test('it can push valid items', function () {
        $collection = new StubTypedCollection();
        $collection->push(new \stdClass());

        expect($collection)->toHaveCount(1);
    });

    test('it throws exception when pushing invalid item', function () {
        $collection = new StubTypedCollection();

        expect(fn() => $collection->push('invalid'))
            ->toThrow(InvalidArgumentException::class);
    });

    test('it can add valid item', function () {
        $collection = new StubTypedCollection();
        $collection->add(new \stdClass());

        expect($collection)->toHaveCount(1);
    });

    test('it can put valid item', function () {
        $collection = new StubTypedCollection();
        $collection->put('key', new \stdClass());

        expect($collection)->toHaveCount(1)
            ->and($collection->get('key'))->toBeInstanceOf(\stdClass::class);
    });

    test('it can prepend valid item', function () {
        $collection = new StubTypedCollection();
        $collection->prepend(new \stdClass());

        expect($collection)->toHaveCount(1);
    });

    test('it can filter items and return same collection type', function () {
        $item1 = new \stdClass();
        $item1->id = 1;
        $item2 = new \stdClass();
        $item2->id = 2;

        $collection = new StubTypedCollection([$item1, $item2]);
        $filtered = $collection->filter(fn($item) => $item->id === 1);

        expect($filtered)->toBeInstanceOf(StubTypedCollection::class)
            ->and($filtered)->toHaveCount(1);
    });

    test('it can map items and return generic collection', function () {
        $collection = new StubTypedCollection([new \stdClass()]);
        $mapped = $collection->map(fn($item) => 'transformed');

        expect($mapped)->toBeInstanceOf(\Illuminate\Support\Collection::class)
            ->and($mapped)->not->toBeInstanceOf(StubTypedCollection::class)
            ->and($mapped->first())->toBe('transformed');
    });

    test('from static method', function () {
        $collection = StubTypedCollection::from([new \stdClass()]);
        expect($collection)->toBeInstanceOf(StubTypedCollection::class)
            ->and($collection)->toHaveCount(1);
    });
});
