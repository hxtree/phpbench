<?php

namespace PhpBench\Report\Model;

use ArrayIterator;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int,Report>
 */
final class Reports implements IteratorAggregate
{
    /**
     * @var Report[]
     */
    private $reports;

    /**
     * @param Report[] $reports
     */
    private function __construct(array $reports)
    {
        $this->reports = $reports;
    }

    public static function fromReport(Report $report): self
    {
        return new self([$report]);
    }

    public static function fromReports(Report ...$reports): self
    {
        return new self($reports);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function merge(Reports $reports): self
    {
        return new self(array_merge($this->reports, $reports->reports));
    }

    public function getIterator()
    {
        return new ArrayIterator($this->reports);
    }
}
