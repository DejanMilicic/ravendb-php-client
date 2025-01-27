<?php

namespace RavenDB\Documents\Indexes;

use Closure;
use RavenDB\Constants\DocumentsIndexingFields;
use RavenDB\Documents\Indexes\Spatial\SpatialOptionsFactory;
use RavenDB\Documents\Indexes\Spatial\SpatialOptionsMap;
use RavenDB\Exceptions\IllegalArgumentException;
use RavenDB\Type\StringArray;
use RavenDB\Type\StringSet;

// !status: DONE
abstract class AbstractGenericIndexCreationTask extends AbstractIndexCreationTaskBase
{
    protected ?string $reduce = null;

    protected ?FieldStorageMap $storesStrings = null;
    protected ?FieldIndexingMap $indexesStrings = null;
    protected ?StringArray $analyzersStrings = null;
    protected ?StringSet $indexSuggestions = null;
    protected ?FieldTermVectorMap $termVectorsStrings = null;
    protected ?SpatialOptionsMap $spatialOptionsStrings = null;

    protected ?string $outputReduceToCollection = null;
    protected ?string $patternForOutputReduceToCollectionReferences = null;
    protected ?string $patternReferencesCollectionName = null;

    public function __construct() {
        parent::__construct();

        $this->storesStrings = new FieldStorageMap();
        $this->indexesStrings = new FieldIndexingMap();
        $this->analyzersStrings = new StringArray();
        $this->indexSuggestions = new StringSet();
        $this->termVectorsStrings = new FieldTermVectorMap();
        $this->spatialOptionsStrings = new SpatialOptionsMap();
    }

    /**
     * Gets a value indicating whether this instance is map reduce index definition
     * @return bool if index is of type: Map/Reduce
     */
    public function isMapReduce(): bool
    {
        return $this->reduce != null;
    }

    // AbstractGenericIndexCreationTask

    /**
     * Register a field to be indexed
     * @param string|null $field
     * @param FieldIndexing|null $indexing
     */
    protected function index(?string $field, ?FieldIndexing $indexing): void
    {
        $this->indexesStrings->offsetSet($field, $indexing);
    }

    /**
     * Register a field to be spatially indexed
     * @param string|null $field Field
     * @param Closure $indexing factory for spatial options
     */
    protected function spatial(?string $field, Closure $indexing): void
    {
        $this->spatialOptionsStrings->offsetSet($field, $indexing(new SpatialOptionsFactory()));
    }

    protected function storeAllFields(?FieldStorage $storage): void
    {
        $this->storesStrings->offsetSet(DocumentsIndexingFields::ALL_FIELDS, $storage);
    }

    /**
     * Register a field to be stored
     * @param string|null $field Field name
     * @param FieldStorage|null $storage Field storage value to use
     */
    protected function store(?string $field, ?FieldStorage $storage): void
    {
        $this->storesStrings->offsetSet($field, $storage);
    }

    /**
     * Register a field to be analyzed
     * @param string|null $field Field name
     * @param string|null $analyzer analyzer to use
     */
    protected function analyze(?string $field, ?string $analyzer): void
    {
        $this->analyzersStrings->offsetSet($field, $analyzer);
    }

    /**
     * Register a field to have term vectors
     * @param string|null $field Field name
     * @param FieldTermVector|null $termVector TermVector type
     */
    protected function termVector(?string $field, ?FieldTermVector $termVector): void
    {
        $this->termVectorsStrings->offsetSet($field, $termVector);
    }

    protected function suggestion(?string $field): void
    {
        $this->indexSuggestions->append($field);
    }

    protected function addAssembly(?AdditionalAssembly $assembly): void
    {
        if ($assembly == null) {
            throw new IllegalArgumentException("Assembly cannot be null");
        }
        $set = $this->getAdditionalAssemblies();
        if ($set == null) {
            $set = new AdditionalAssemblySet();

        }
        $set->append($assembly);
        $this->setAdditionalAssemblies($set);
    }
}
