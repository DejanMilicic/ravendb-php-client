<?php

namespace RavenDB\Type;

class TypedArray extends ExtendedArrayObject implements TypedArrayInterface
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

    protected function validateValue($value): void
    {
        if (! $value instanceof $this->type) {
            throw new \TypeError(
                sprintf("Only values of type %s are supported", $this->type)
            );
        }
    }


}
