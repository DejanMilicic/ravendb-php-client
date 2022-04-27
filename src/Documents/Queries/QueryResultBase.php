<?php

namespace RavenDB\Documents\Queries;

use DateTimeInterface;
use RavenDB\Documents\Queries\Timings\QueryTimings;
use RavenDB\Http\ResultInterface;
use RavenDB\Type\StringArray;

use Symfony\Component\Serializer\Annotation\SerializedName;

class QueryResultBase
{
    /** @SerializedName ("Results") */
    private ?array $results = null;

    /** @ SerializedName ("Includes") */
    private ?IncludesArray $includes = null;

//    private ObjectNode counterIncludes;
//
//    private Map<String, String[]> includedCounterNames;
//
//    private ObjectNode timeSeriesIncludes;
//
//    private ObjectNode compareExchangeValueIncludes;

    /** @SerializedName ("IncludedPaths") */
    private StringArray $includedPaths;

    /** @SerializedName ("IsStale") */
    private bool $isStale = false;

    /** @SerializedName ("IndexTimestamp") */
    private ?DateTimeInterface $indexTimestamp = null;

    /** @SerializedName ("IndexName") */
    private string $indexName;

    /** @SerializedName ("ResultEtag") */
    private int $resultEtag = 0;

    /** @SerializedName ("LastQueryTime") */
    private ?DateTimeInterface $lastQueryTime = null;

    /** @SerializedName ("NodeTag") */
    private string $nodeTag;

    private ?QueryTimings $timings = null;

    public function __construct()
    {
        $this->includedPaths = new StringArray();
    }

    /**
     * Gets the document resulting from this query.
     * @return array Query results
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Sets the document resulting from this query.
     * @param array $results Sets the query results
     */
    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    /**
     * Gets the document included in the result.
     * @return ?IncludesArray Query includes
     */
    public function getIncludes(): ?IncludesArray
    {
        return $this->includes;
    }

    /**
     * Sets the document included in the result.
     * @param ?IncludesArray $includes Sets the value
     */
    public function setIncludes(?IncludesArray $includes): void
    {
        $this->includes = $includes;
    }

//    /**
//     * @return Gets the Counters included in the result.
//     */
//    public ObjectNode getCounterIncludes() {
//        return counterIncludes;
//    }
//
//    /**
//     * @param counterIncludes Sets the Counters included in the result.
//     */
//    public void setCounterIncludes(ObjectNode counterIncludes) {
//        this.counterIncludes = counterIncludes;
//    }
//
//    /**
//     * @return The names of all the counters that the server was asked to include in the result, by document id.
//     */
//    public Map<String, String[]> getIncludedCounterNames() {
//        return includedCounterNames;
//    }
//
//    /**
//     * @param includedCounterNames The names of all the counters that the server was asked to include in the result, by document id.
//     */
//    public void setIncludedCounterNames(Map<String, String[]> includedCounterNames) {
//        this.includedCounterNames = includedCounterNames;
//    }
//
//    /**
//     * @return Gets the TimeSeries included in the result.
//     */
//    public ObjectNode getTimeSeriesIncludes() {
//        return timeSeriesIncludes;
//    }
//
//    /**
//     * @param timeSeriesIncludes Sets the TimeSeries included in the result.
//     */
//    public void setTimeSeriesIncludes(ObjectNode timeSeriesIncludes) {
//        this.timeSeriesIncludes = timeSeriesIncludes;
//    }
//
//    /**
//     * @return Gets the Compare Exchange Values included in the result.
//     */
//    public ObjectNode getCompareExchangeValueIncludes() {
//        return compareExchangeValueIncludes;
//    }
//
//    /**
//     * @param compareExchangeValueIncludes Sets the Compare Exchange Values included in the result.
//     */
//    public void setCompareExchangeValueIncludes(ObjectNode compareExchangeValueIncludes) {
//        this.compareExchangeValueIncludes = compareExchangeValueIncludes;
//    }

    /**
     * The paths that the server included in the results
     * @return StringArray Included paths
     */
    public function getIncludedPaths(): StringArray
    {
        return $this->includedPaths;
    }

    /**
     * The paths that the server included in the results
     * @param StringArray $includedPaths Sets the value
     */
    public function setIncludedPaths(StringArray $includedPaths): void
    {
        $this->includedPaths = $includedPaths;
    }

    /**
     * Gets a value indicating whether the index is stale.
     * @return bool true if index results are stale
     */
    public function isStale(): bool
    {
        return $this->isStale;
    }

    /**
     * Sets a value indicating whether the index is stale.
     * @param bool $stale Sets the value
     */
    public function setStale(bool $stale): void
    {
        $this->isStale = $stale;
    }

    /**
     * The last time the index was updated.
     * This can be used to determine the freshness of the data.
     * @return DateTimeInterface index timestamp
     */
    public function getIndexTimestamp(): DateTimeInterface
    {
        return $this->indexTimestamp;
    }

    /**
     * The last time the index was updated.
     * This can be used to determine the freshness of the data.
     * @param DateTimeInterface $indexTimestamp Sets the value
     */
    public function setIndexTimestamp(DateTimeInterface $indexTimestamp): void
    {
        $this->indexTimestamp = $indexTimestamp;
    }

    /**
     * The index used to answer this query
     * @return string Used index name
     */
    public function getIndexName(): string
    {
        return $this->indexName;
    }

    /**
     * The index used to answer this query
     * @param string $indexName Sets the value
     */
    public function setIndexName(string $indexName): void
    {
        $this->indexName = $indexName;
    }

    /**
     * The ETag value for this index current state, which include what docs were indexed,
     * what document were deleted, etc.
     * @return int result etag
     */
    public function getResultEtag(): int
    {
        return $this->resultEtag;
    }

    /**
     * The ETag value for this index current state, which include what docs were indexed,
     * what document were deleted, etc.
     * @param int $resultEtag Sets the value
     */
    public function setResultEtag(int $resultEtag): void
    {
        $this->resultEtag = $resultEtag;
    }

    /**
     * The timestamp of the last time the index was queried
     * @return DateTimeInterface Last query time
     */
    public function getLastQueryTime(): ?DateTimeInterface
    {
        return $this->lastQueryTime;
    }

    /**
     * The timestamp of the last time the index was queried
     * @param ?DateTimeInterface $lastQueryTime Sets the value
     */
    public function setLastQueryTime(?DateTimeInterface $lastQueryTime): void {
        $this->lastQueryTime = $lastQueryTime;
    }

    /**
     * @return string Tag of a cluster node which responded to the query
     */
    public function getNodeTag(): string
    {
        return $this->nodeTag;
    }

    /**
     * @param string $nodeTag Tag of a cluster node which responded to the query
     */
    public function setNodeTag(string $nodeTag): void
    {
        $this->nodeTag = $nodeTag;
    }

    /**
     * @return ?QueryTimings Detailed timings for various parts of a query (Lucene search, loading documents, transforming results) - if requested.
     */
    public function getTimings(): ?QueryTimings
    {
        return $this->timings;
    }

    /**
     * @param ?QueryTimings $timings Detailed timings for various parts of a query (Lucene search, loading documents, transforming results) - if requested.
     */
    public function setTimings(?QueryTimings $timings) {
        $this->timings = $timings;
    }
}
