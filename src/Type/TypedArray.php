<?php

namespace RavenDB\Type;

class TypedArray extends \ArrayObject implements TypedArrayInterface, \JsonSerializable
{
    protected string $type;

    public static function forType(string $type): self
    {
        return new self($type);
    }

    public function getType(): string
    {
        return $this->type;
    }

    protected function __construct(string $type)
    {
        $this->type = $type;

        if (!class_exists($type)) {
            throw new \TypeError(
                sprintf("Typed array cant be instantiated. CLass: >> %s <<  does not exists! ", $this->type)
            );
        }

        parent::__construct();
    }

    public function offsetSet($key, $value)
    {
        if (! $value instanceof $this->type) {
            throw new \TypeError(
                sprintf("Only values of type %s are supported", $this->type)
            );
        }

        parent::offsetSet($key, $value); // TODO: Change the autogenerated stub
    }

    public function removeValue($value): void
    {
        if(($key = array_search($value, $this->getArrayCopy(), true)) !== FALSE) {
            $this->offsetUnset($key);
        }
    }

    public function clear(): void
    {
        foreach ($this as $key => $value) {
            $this->offsetUnset($key);
        }
    }

    public function jsonSerialize(): array
    {
        return $this->getArrayCopy();
    }
}