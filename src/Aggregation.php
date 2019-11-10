<?php

namespace AHP;

use ONGR\ElasticsearchDSL\Aggregation\Metric\AvgAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\SumAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\DateHistogramAggregation;

class Aggregation
{
    /**
     * A single-value metrics aggregation that computes the average of numeric values that are extracted from the aggregated documents.
     *
     * @see https://github.com/ongr-io/ElasticsearchDSL/blob/master/docs/Aggregation/Metric/Avg.md
     *
     * @param  string  $name
     * @param  string|null  $field
     * @param  string|null  $script
     * @return \ONGR\ElasticsearchDSL\Aggregation\Metric\AvgAggregation
     */
    public function avg(string $name, string $field = null, string $script = null): AvgAggregation
    {
        return new AvgAggregation($name, $field, $script);
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
     * @return \ONGR\ElasticsearchDSL\Aggregation\Bucketing\DateHistogramAggregation
     */
    public function dateHistogram(string $name, string $field = null, string $interval = null, string $format = null): DateHistogramAggregation
    {
        return new DateHistogramAggregation($name, $field, $interval, $format);
    }

    /**
     * A multi-bucket value source based aggregation where buckets are dynamically built - one per unique value.
     *
     * @see https://github.com/ongr-io/ElasticsearchDSL/blob/master/docs/Aggregation/Bucketing/Terms.md
     *
     * @param  string  $name
     * @param  string|null  $field
     * @param  null  $script
     * @param  array  $parameters
     * @return \ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation
     */
    public function termsAggregation(string $name, string $field = null, array $parameters = [], $script = null): TermsAggregation
    {
        $aggregation = new TermsAggregation($name, $field, $script);

        $this->addParameters($aggregation, $parameters);

        return $aggregation;
    }

    /**
     * A single-value metrics aggregation that sums up numeric values that are extracted from the aggregated documents.
     *
     * @see https://github.com/ongr-io/ElasticsearchDSL/blob/master/docs/Aggregation/Metric/Sum.md
     *
     * @param  string  $name
     * @param  string|null  $field
     * @param  array  $parameters
     * @param  null  $script
     * @return \ONGR\ElasticsearchDSL\Aggregation\Metric\SumAggregation
     */
    public function sum(string $name, string $field = null, array $parameters = [], $script = null): SumAggregation
    {
        $aggregation = new SumAggregation($name, $field, $script);

        $this->addParameters($aggregation, $parameters);

        return $aggregation;
    }

    /**
     * Convenient way to add query parameters.
     *
     * @param  $aggregation
     * @param  array  $parameters
     */
    private function addParameters($aggregation, array $parameters): void
    {
        foreach ($parameters as $key => $parameter) {
            $aggregation->addParameter($key, $parameter);
        }
    }
}
