<?php

namespace RavenDB\Documents\Session;

interface DocumentQueryInterface
    extends DocumentQueryBaseInterface, DocumentQueryBaseSingleInterface, EnumerableQueryInterface
{
//    String getIndexName();
//
//    Class<T> getQueryClass();
//
//    /**
//     * Whether we should apply distinct operation to the query on the server side
//     * @return true if server should return distinct results
//     */
//    boolean isDistinct();
//
//    /**
//     * Returns the query result. Accessing this property for the first time will execute the query.
//     * @return query result
//     */
//    QueryResult getQueryResult();
//
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
//
//    /**
//     * Changes the return type of the query
//     * @param <TResult> class of result
//     * @param resultClass class of result
//     * @return Document query
//     */
//    <TResult> IDocumentQuery<TResult> ofType(Class<TResult> resultClass);
//
//    IGroupByDocumentQuery<T> groupBy(String fieldName, String... fieldNames);
//
//    IGroupByDocumentQuery<T> groupBy(GroupBy field, GroupBy... fields);
//
//    IDocumentQuery<T> moreLikeThis(Consumer<IMoreLikeThisBuilderForDocumentQuery<T>> builder);
//
//    IAggregationDocumentQuery<T> aggregateBy(Consumer<IFacetBuilder<T>> builder);
//
//    IAggregationDocumentQuery<T> aggregateBy(FacetBase facet);
//
//    IAggregationDocumentQuery<T> aggregateBy(FacetBase... facet);
//
//    IAggregationDocumentQuery<T> aggregateUsing(String facetSetupDocumentId);
//
//    ISuggestionDocumentQuery<T> suggestUsing(SuggestionBase suggestion);
//
//    ISuggestionDocumentQuery<T> suggestUsing(Consumer<ISuggestionBuilder<T>> builder);


    public function andAlso(bool $wrapPreviousQueryClauses = false): DocumentQueryInterface;

    public function orElse(): DocumentQueryInterface;

    /**
     * @param string $fieldName
     * @param mixed|MethodCall $value
     * @param bool $exact
     */
    public function whereEquals(string $fieldName, $value, bool $exact = false): DocumentQueryInterface;

    public function whereEqualsWithParams(WhereParams $whereParams): DocumentQueryInterface;
}