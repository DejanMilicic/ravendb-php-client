<?php

namespace RavenDB\Documents\Operations\TimeSeries;

use RavenDB\Exceptions\IllegalArgumentException;
use RavenDB\Primitives\TimeValue;

// !status: DONE
class RawTimeSeriesPolicy extends TimeSeriesPolicy
{
    public const POLICY_STRING = "rawpolicy"; // must be lower case

    public static RawTimeSeriesPolicy $DEFAULT_POLICY;

    /**
     * @throws IllegalArgumentException
     * @throws \RavenDB\Exceptions\IllegalStateException
     */
    public function __construct(?TimeValue $retentionTime = null)
    {
        if ($retentionTime == null) {
            $retentionTime = TimeValue::maxValue();
        }

        if ($retentionTime->compareTo(TimeValue::zero()) <= 0) {
            throw new IllegalArgumentException("Must be greater than zero");
        }

        parent::__construct(self::POLICY_STRING, TimeValue::maxValue(), $retentionTime);
    }
}

RawTimeSeriesPolicy::$DEFAULT_POLICY =  new RawTimeSeriesPolicy();
