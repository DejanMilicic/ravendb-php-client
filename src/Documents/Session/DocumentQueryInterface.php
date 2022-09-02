<?php

namespace RavenDB\Documents\Session;

use RavenDB\Documents\Queries\Facets\AggregationDocumentQueryInterface;
use RavenDB\Documents\Queries\Facets\FacetBase;
use RavenDB\Documents\Queries\GroupBy;
use RavenDB\Documents\Queries\QueryResult;

interface DocumentQueryInterface
    extends DocumentQueryBaseInterface, DocumentQueryBaseSingleInterface, EnumerableQueryInterface
{
//    String getIndexName();
//
//    Class<T> getQueryClass();

    /**
     * Whether we should apply distinct operation to the query on the server side
     *
     * @return bool true if server should return distinct results
     */
    function isDistinct(): bool;

    /**
     * Returns the query result. Accessing this property for the first time will execute the query.
     *
     * @return QueryResult query result
     */
    function getQueryResult(): QueryResult;

//    /**
//     * Selects the specified fields directly from the index if the are stored. If the field is not stored in index, value
//     * will come from document directly.
//     * @param <TProjection> projection class
//     * @param projectionClass projection class
//     * @return Document query
//     */
//    <TProjection> IDocumentQuery<TProjection> selectFields(Class<TProjection> projectionClass);
//
//    /**
//     * Selects the specified fields directly from the index if the are stored. If the field is not stored in index, value
//     * will come from document directly.
//     * @param <TProjection> projection class
//     * @param projectionClass projection class
//     * @param projectionBehavior projection behavior to use
//     * @return Document query
//     */
//    <TProjection> IDocumentQuery<TProjection> selectFields(Class<TProjection> projectionClass, ProjectionBehavior projectionBehavior);
//
//    /**
//     * Selects the specified fields directly from the index if the are stored. If the field is not stored in index, value
//     * will come from document directly.
//     * @param <TProjection> projection class
//     * @param projectionClass projection class
//     * @param fields Fields to fetch
//     * @return Document query
//     */
//    <TProjection> IDocumentQuery<TProjection> selectFields(Class<TProjection> projectionClass, String... fields);
//
//    /**
//     * Selects the specified fields according to the given projection behavior.
//     * @param projectionClass projection class
//     * @param projectionBehavior projection behavior to use
//     * @param fields Fields to fetch
//     * @param <TProjection> projection class
//     * @return Document query
//     */
//    <TProjection> IDocumentQuery<TProjection> selectFields(Class<TProjection> projectionClass, ProjectionBehavior projectionBehavior, String... fields);
//
//    /**
//     * Selects the specified fields directly from the index if the are stored. If the field is not stored in index, value
//     * will come from document directly.
//     * @param <TProjection> projection class
//     * @param projectionClass projection class
//     * @param queryData Query data to use
//     * @return Document query
//     */
//    <TProjection> IDocumentQuery<TProjection> selectFields(Class<TProjection> projectionClass, QueryData queryData);
//
//    /**
//     * Selects a Time Series Aggregation based on
//     * a time series query generated by an ITimeSeriesQueryBuilder.
//     * @param clazz Result class
//     * @param timeSeriesQuery query provider
//     * @param <TTimeSeries> Result class
//     * @return Document query
//     */
//    <TTimeSeries> IDocumentQuery<TTimeSeries> selectTimeSeries(Class<TTimeSeries> clazz, Consumer<ITimeSeriesQueryBuilder> timeSeriesQuery);

    /**
     * Changes the return type of the query
     * @param string $resultClass class of result
     *
     * @return DocumentQueryInterface Document query
     */
    function ofType(string $resultClass): DocumentQueryInterface;

    /**
     * @param string|GroupBy $fieldName
     * @param string|GroupBy ...$fieldNames
     *
     * @return GroupByDocumentQueryInterface
     */
    public function groupBy($fieldName, ...$fieldNames): GroupByDocumentQueryInterface;

//    IDocumentQuery<T> moreLikeThis(Consumer<IMoreLikeThisBuilderForDocumentQuery<T>> builder);

    /**
     * @param Callable|FacetBase $builderOrFacets
     *
     * @return AggregationDocumentQueryInterface
     */
    public function aggregateBy(...$builderOrFacets): AggregationDocumentQueryInterface;

//    IAggregationDocumentQuery<T> aggregateUsing(String facetSetupDocumentId);
//
//    ISuggestionDocumentQuery<T> suggestUsing(SuggestionBase suggestion);
//
//    ISuggestionDocumentQuery<T> suggestUsing(Consumer<ISuggestionBuilder<T>> builder);


    public function toString(bool $compatibilityMode = false): string;
}
