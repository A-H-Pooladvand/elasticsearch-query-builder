<?php

namespace AHP;

use Illuminate\Support\Collection as LaravelCollection;

class Collection extends LaravelCollection
{
    /**
     * Get the values of a given key.
     *
     * @param $value
     * @param  null  $key
     * @return self
     */
    public function pluck($value, $key = null): self
    {
        $columns = explode('.', $value);

        if (! empty($this->items['_shards'])) {
            $this->source();
        }

        foreach ($columns as $item) {
            $this->items = $this->plucker($this->items, $item);
        }

        return new static($this->items);
    }

    /**
     * Get second level of hits (_source).
     *
     * @return self
     */
    public function source(): self
    {
        $this->items = $this->hits();

        $this->items = array_map(static function ($item) {
            return $item['_source'];
        }, $this->items['hits']);

        return new static($this->items);
    }

    /**
     * Get first level of hits.
     *
     * @return array
     */
    private function hits(): array
    {
        return $this->items['hits'];
    }

    /**
     * Plucks given items based on provided column.
     *
     * @param  array  $items
     * @param  string  $column
     * @return array
     */
    private function plucker(array $items, string $column): array
    {
        return array_map(static function ($item) use ($column) {
            if (! is_array($item)) {
                return $item;
            }

            $reserved = $item;

            if (array_key_exists($column, $reserved)) {
                return $item[$column];
            }

            return array_shift($reserved)[$column];
        }, $items);
    }

    /**
     * Count elements of an object
     *
     * @return int
     */
    public function total(): int
    {
        return $this->hits()['total'];
    }

    /**
     * Get aggregation bucket.
     *
     * @param  mixed  ...$index
     * @return \AHP\Collection
     */
    public function aggregations(...$index): self
    {
        if (empty($index)) {
            $this->items = $this->items['aggregations'];

            return new static($this->items);
        }

        $items = [];
        array_map(function (string $index) use (&$items) {
            $items[$index] = array_map(static function ($item) {
                return [
                    'title' => $item['key'],
                    'count' => $item['doc_count'],
                ];
            }, $this->items['aggregations'][$index]['buckets']);
        }, $index);

        $this->items = count($index) > 1 ? $items : reset($items);

        return new static($this->items);
    }

    /**
     * Get aggregation bucket.
     *
     * @return $this
     */
    public function buckets(): self
    {
        return new static($this->aggregations()['hits']['buckets']);
    }
}
