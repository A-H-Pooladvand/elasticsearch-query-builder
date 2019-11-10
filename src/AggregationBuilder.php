<?php

namespace AHP;

use Closure;
use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\SumAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\RangeAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\DateHistogramAggregation;

trait AggregationBuilder
{
    /**
     * Aggregation container.
     *
     * @var array $aggregations
     */
    private $aggregations = [];

    /**
     * A multi-bucket value source based aggregation where buckets are dynamically built - one per unique value.
     *
     * @see https://github.com/ongr-io/ElasticsearchDSL/blob/master/docs/Aggregation/Bucketing/Terms.md
     *
     * @param  string  $name
     * @param  string|null  $field
     * @param  null  $script
     * @param  array  $parameters
     * @return self
     */
    public function termsAggregation(string $name, string $field = null, array $parameters = [], $script = null): self
    {
        $aggregation = new TermsAggregation($name, $field, $script);

        $this->addParameters($aggregation, $parameters);

        $this->setAggregations($aggregation);

        return $this;
    }

    /**
     * Set aggregations container.
     *
     * @param $aggregation
     */
    private function setAggregations($aggregation): void
    {
        $this->aggregations[] = $aggregation;
    }

    /**
     * A multi-bucket aggregation similar to the histogram except it can only be applied on date values.
     * Example of expressions for interval: year, quarter, month, week, day, hour, minute, second
     *
     * @see https://github.com/ongr-io/ElasticsearchDSL/blob/master/docs/Aggregation/Bucketing/DateHistogram.md
     *
     * @param  string  $name
     * @param  string|null  $field
     * @param  string|null  $interval
     * @param  string|null  $format
     * @param  \Closure|null  $callable
     * @return self
     */
    public function dateHistogram(string $name, string $field = null, string $interval = null, string $format = null, Closure $callable = null): self
    {
        $aggregation = new DateHistogramAggregation($name, $field, $interval ?? 'day', $format);

        if (isset($callable)) {
            $aggregation = $callable($aggregation, $this->aggregation);
        }

        $this->setAggregations($aggregation);

        return $this;
    }

    /**
     * A single-value metrics aggregation that sums up numeric values that are extracted from the aggregated documents.
     *
     * @see https://github.com/ongr-io/ElasticsearchDSL/blob/master/docs/Aggregation/Metric/Sum.md
     *
     * @param  string  $name
     * @param  string|null  $field
     * @param  null  $script
     * @param  \Closure|null  $callable
     * @return self
     */
    public function sum($name, $field = null, $script = null, Closure $callable = null): self
    {
        $aggregation = new SumAggregation($name, $field, $script);

        if (isset($callable)) {
            $aggregation = $callable($aggregation, $this->aggregation);
        }

        $this->setAggregations($aggregation);

        return $this;
    }

    /**
     * A multi-bucket value source based aggregation that enables the user to define a set of ranges - each representing a bucket.
     *
     * @see https://github.com/ongr-io/ElasticsearchDSL/blob/master/docs/Aggregation/Bucketing/Range.md
     *
     * @param  string  $name
     * @param  string|null  $field
     * @param  array  $ranges
     * @param  bool  $keyed
     * @param  \Closure|null  $callable
     * @return self
     */
    public function rangeAggregation(string $name, string $field = null, array $ranges = [], bool $keyed = false, Closure $callable = null): self
    {
        // Amirhossein: This range query may not be as expected
        // its just a left alone code please visit @see link and make the query fully supported.
        $aggregation = new RangeAggregation($name, $field, $ranges, $keyed);

        if (isset($callable)) {
            $aggregation = $callable($aggregation, $this->aggregation);
        }

        $this->setAggregations($aggregation);

        return $this;
    }

    /**
     * Convenient way to add query parameters.
     *
     * @param  \ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation  $aggregation
     * @param  array  $parameters
     */
    private function addParameters(AbstractAggregation $aggregation, array $parameters): void
    {
        foreach ($parameters as $key => $parameter) {
            $aggregation->addParameter($key, $parameter);
        }
    }
}
