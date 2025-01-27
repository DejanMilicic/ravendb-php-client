<?php

namespace RavenDB\Documents\Operations\TimeSeries;

// !status: DONE
class TimeSeriesCollectionConfiguration
{
    private bool $disabled;

    private TimeSeriesPolicyArray $policies;
    private RawTimeSeriesPolicy $rawPolicy;

    public function __construct()
    {
        $this->policies = new TimeSeriesPolicyArray();
        $this->rawPolicy = RawTimeSeriesPolicy::$DEFAULT_POLICY;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }

    /**
     * Specify roll up and retention policy.
     * Each policy will create a new time-series aggregated from the previous one
     * @return TimeSeriesPolicyArray roll up policies
     */
    public function getPolicies(): TimeSeriesPolicyArray {
        return $this->policies;
    }

    /**
     * Specify roll up and retention policy.
     * Each policy will create a new time-series aggregated from the previous one
     * @param TimeSeriesPolicyArray $policies roll up policies to use
     */
    public function setPolicies(TimeSeriesPolicyArray $policies): void
    {
        $this->policies = $policies;
    }

    /**
     * Specify a policy for the original time-series
     * @return RawTimeSeriesPolicy raw time series policy
     */
    public function getRawPolicy(): RawTimeSeriesPolicy
    {
        return $this->rawPolicy;
    }

    /**
     * Specify a policy for the original time-series
     * @param RawTimeSeriesPolicy $rawPolicy raw time series policy to use
     */
    public function setRawPolicy(RawTimeSeriesPolicy $rawPolicy): void
    {
        $this->rawPolicy = $rawPolicy;
    }

    public static function isRaw(TimeSeriesPolicy $policy): bool {
        return RawTimeSeriesPolicy::$DEFAULT_POLICY->getName() == $policy->getName();
    }
}
