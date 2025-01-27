<?php

namespace RavenDB\Documents\Operations\Indexes;

use RavenDB\Documents\Conventions\DocumentConventions;
use RavenDB\Exceptions\IllegalArgumentException;
use RavenDB\Http\HttpRequest;
use RavenDB\Http\HttpRequestInterface;
use RavenDB\Http\ServerNode;
use RavenDB\Http\VoidRavenCommand;
use RavenDB\Utils\RaftIdGenerator;

// !status: DONE
class SetIndexLockCommand extends VoidRavenCommand
{
    private array $parameters;

    public function __construct(?DocumentConventions $conventions, ?IndexLockParameters $parameters)
    {
        parent::__construct();

        if ($conventions == null) {
            throw new IllegalArgumentException("Conventions cannot be null");
        }

        if ($parameters == null) {
            throw new IllegalArgumentException("Parameters cannot be null");
        }

        $this->parameters = $this->getMapper()->normalize($parameters);
    }

    public function createUrl(ServerNode $serverNode): string
    {
        return $serverNode->getUrl() .
            "/databases/" . $serverNode->getDatabase() .
            "/indexes/set-lock";
    }

    public function createRequest(ServerNode $serverNode): HttpRequestInterface
    {
        $options = [
            'json' => $this->parameters,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ];

        return new HttpRequest($this->createUrl($serverNode), HttpRequest::POST, $options);
    }

    public function getRaftUniqueRequestId(): string
    {
        return RaftIdGenerator::newId();
    }
}
