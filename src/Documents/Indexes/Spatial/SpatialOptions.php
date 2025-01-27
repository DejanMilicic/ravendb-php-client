<?php

namespace RavenDB\Documents\Indexes\Spatial;

// !status: DONE
class SpatialOptions
{
    // about 4.78 meters at the equator, should be good enough (see: http://unterbahn.com/2009/11/metric-dimensions-of-geohash-partitions-at-the-equator/)
    public const DEFAULT_GEOHASH_LEVEL = 9;

    // about 4.78 meters at the equator, should be good enough
    public const DEFAULT_QUAD_TREE_LEVEL = 23;

    public function __construct(?SpatialOptions $options = null)
    {
        if ($options != null) {
            $this->_initFrom($options);
            return;
        }

        $this->_initWithDefaultValues();
    }

    private function _initWithDefaultValues(): void
    {
        $this->type = SpatialFieldType::geography();
        $this->strategy = SpatialSearchStrategy::geohashPrefixTree();
        $this->maxTreeLevel = self::DEFAULT_GEOHASH_LEVEL;
        $this->minX = -180;
        $this->maxX = 180;
        $this->minY = -90;
        $this->maxY = 90;
        $this->units = SpatialUnits::kilometers();
    }

    private function _initFrom(SpatialOptions $options)
    {
        $this->type = $options->type;
        $this->strategy = $options->strategy;
        $this->maxTreeLevel = $options->maxTreeLevel;
        $this->minX = $options->minX;
        $this->maxX = $options->maxX;
        $this->minY = $options->minY;
        $this->maxY = $options->maxY;
        $this->units = $options->units;
    }

    private SpatialFieldType $type;
    private SpatialSearchStrategy $strategy;
    private int $maxTreeLevel;
    private float $minX;
    private float $maxX;
    private float $minY;
    private float $maxY;

    // Circle radius units, only used for geography  indexes
    private SpatialUnits $units;

    public function getType(): SpatialFieldType
    {
        return $this->type;
    }

    public function setType(SpatialFieldType $type): void
    {
        $this->type = $type;
    }

    public function getStrategy(): SpatialSearchStrategy
    {
        return $this->strategy;
    }

    public function setStrategy(SpatialSearchStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function getMaxTreeLevel(): int
    {
        return $this->maxTreeLevel;
    }

    public function setMaxTreeLevel(int $maxTreeLevel): void
    {
        $this->maxTreeLevel = $maxTreeLevel;
    }

    public function getMinX(): float
    {
        return $this->minX;
    }

    public function setMinX(float $minX): void
    {
        $this->minX = $minX;
    }

    public function getMaxX(): float
    {
        return $this->maxX;
    }

    public function setMaxX(float $maxX): void
    {
        $this->maxX = $maxX;
    }

    public function getMinY(): float
    {
        return $this->minY;
    }

    public function setMinY(float $minY): void
    {
        $this->minY = $minY;
    }

    public function getMaxY(): float
    {
        return $this->maxY;
    }

    public function setMaxY(float $maxY): void
    {
        $this->maxY = $maxY;
    }

    public function getUnits(): SpatialUnits
    {
        return $this->units;
    }

    public function setUnits(SpatialUnits $units): void
    {
        $this->units = $units;
    }


// @todo: remote it later - ignore this for now - this is autogenerated code from java that is used in HashMap
//      and we don't need it in PHP client
//
//    public boolean equals(Object obj) {
//        if (this == obj)
//            return true;
//        if (obj == null)
//            return false;
//        if (getClass() != obj.getClass())
//            return false;
//        SpatialOptions other = (SpatialOptions) obj;
//
//        boolean result = type == other.getType() && strategy == other.strategy;
//        if (type == SpatialFieldType.GEOGRAPHY) {
//            result = result && units == other.units;
//        }
//        if (strategy != SpatialSearchStrategy.BOUNDING_BOX) {
//            result = result && maxTreeLevel == other.maxTreeLevel;
//            if (type == SpatialFieldType.CARTESIAN) {
//                result = result && minX == other.minX
//                        && maxX == other.maxX
//                        && minY == other.minY
//                        && maxY == other.maxY;
//            }
//        }
//
//        return result;
//    }
//
//    @Override
//    public int hashCode() {
//        HashCodeBuilder builder = new HashCodeBuilder();
//        builder.append(type);
//        builder.append(strategy);
//        if (type == SpatialFieldType.GEOGRAPHY) {
//            builder.append(units.hashCode());
//        }
//        if (strategy != SpatialSearchStrategy.BOUNDING_BOX) {
//            builder.append(maxTreeLevel);
//            if (type == SpatialFieldType.CARTESIAN) {
//                builder.append(minX).append(maxX).append(minY).append(maxY);
//            }
//        }
//
//
//        return builder.hashCode();
//    }
}
