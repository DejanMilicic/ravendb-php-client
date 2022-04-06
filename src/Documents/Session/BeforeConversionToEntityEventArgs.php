<?php

namespace RavenDB\Documents\Session;

use RavenDB\Primitives\EventArgs;

// !status: DONE
class BeforeConversionToEntityEventArgs extends EventArgs
{
    private string $id;
    private object $entity;
    private array $document;
    private InMemoryDocumentSessionOperations $session;

    public function __construct(string $id, object $entity, array $document, InMemoryDocumentSessionOperations $session)
    {
        $this->id = $id;
        $this->entity = $entity;
        $this->document = $document;
        $this->session = $session;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getDocument(): array
    {
        return $this->document;
    }

    public function getSession(): InMemoryDocumentSessionOperations
    {
        return $this->session;
    }
}
