<?php

namespace RavenDB\Http;

// !status: DONE
abstract class VoidRavenCommand extends RavenCommand
{
    public function __construct()
    {
        parent::__construct(null);
        $this->responseType = RavenCommandResponseType::empty();
    }

    public function isReadRequest(): bool
    {
        return false;
    }
}
